<?php
namespace PhpSemvers\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use PhpSemvers\Tools\Context;
use PhpSemvers\Tools\Checks;
use PhpSemvers\Tools\SemVersUtils;
use PhpSemvers\Tools\SemVersBumpEnum;
use PhpSemvers\Tools\GitUtils;
use PhpSemvers\Tools\Path;

class VersionBump extends Command
{
    private Object $whiteacornConfig;
    private function helpText()
    {
        return <<<EOD
"Bumps the current semvers.\n".
"\t1. Gets the latest semvers from git tags,\n".
"\t2. Bumps  one of major|minor|patch component of the latest semversion,\n".
"\t3. Creates a new git tag from the bumped semver and pushes to the github remote (origin), and\n".
"\t4. Save version+branch+commit in the php/config/version.php file or the version file provided in the config file"

EOD;

    }
    /** @return void */
	protected function configure(): void
	{
		parent::configure();
        $helpText = <<<EOD
The <info>v:bump</info> command must be run from the root of a git repo
and the repo must be clean (no uncommitted changes). 

The command reads a config file names <info>.phpsemvers.json</info>
from the current directory. This file is json and has the form:
<info>
{
    "version_file_relpath": ......,
    "extended_semvers" : true|false,
    "git_remote": ......
}
</info>
If keys are absent:
- <info>version_file_relpath</info> defaults to <info>src/Version.php</info>
- <info>extended_semvers</info> defaults to <info>false</info>
- <info>git_remote</info> defaults to <info>origin</info>


If the  <info>extended_semvers</info> flag is not set on the command line
or in the config file the following steps are performed:

1. get the most recent <info>git tag</info> as a semantic version number, fails if there are none
2. increments the semantic version according to the arguments
3. writes the update version to the version file
4. commits the change to the version file
5. pushes that commit to <info>origin</info> and active branch
6. creates a tag with the value of the update semantic version number
7. pushes that tag to <info>origin</info> and active branch


If the <info>extended_semvers</info> flag is set on the command line 
or in the config file then perform the following steps are performed:

1. get the most recent <info>git tag</info> as a semantic version number, fails if there are none
2. increments the semantic version according to the arguments
3. creates a tag with the value of the update semantic version number
4. pushes that tag to <info>origin</info> and active branch
5. get the <info>hash</info> of the latest commit
6. create an extended version string of the form <info>vn.n.n-(branch)commit_hash</info>
3. writes this extended version to the version file

Note in this last case the update version file is not committed. This is a special case
for a project where the repo is deployed with rsync and the version file is deployed
even though it is not in the repo.

EOD;

		$this
			->setName('v:bump')
			->setDescription(
				"Bumps, tag and commit the semantic version for a project git repo."
			)
			->setDefinition([
                new InputArgument('part', InputArgument::REQUIRED,
                    'The <info>part</info> of the semver to bump <info>major</info>|<info>minor</info>|<info>patch</info>'),
                new InputOption('extended_semvers', "x", InputOption::VALUE_NONE,
                    'Writes an extended semantic version string to the version file but does not commit that change'),
//                new InputOption('dryrun', "d", InputOption::VALUE_NONE,'Do not create the new tag and dont push the tag'),
//                new InputOption('no-save', "n", InputOption::VALUE_NONE,'Do not save the new version to the version file')
            ])
			->setHelp($helpText);
	}

    /**
     * @param InputInterface $input Input object.
     * @param OutputInterface $output Output object.
     * @return int
     * @throws \Exception
     */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
        $context = Context::create_from_config_file();
        $cwd = getcwd();
        $extended_semvers = $input->getOption("extended_semvers") || $context->extended_semvers;
        Checks::checkGitRepoIsClean();
        $bumpTypeString = $input->getArgument("part");
        $bumpType = SemVersBumpEnum::tryFrom($bumpTypeString);
        $git = new GitUtils($cwd);
        $branch = $git->getActiveBranch();
        $semvers = SemVersUtils::semversFromGitTag();
        if(is_null($semvers)) {
            throw new \Exception("could not make semvers from tag - probably <info>php_semvers</info> not initialized for this repo");
        }
        if($extended_semvers) {
            $bumpedSemVers = $semvers->bump($bumpType);
            $hash = $git->getCommitHashForBranch($branch);
            SemVersUtils::createTagFromSemVers($bumpedSemVers);
            SemVersUtils::pushSemversTag("origin", $bumpedSemVers);
            $extended_semvers = "{$bumpedSemVers}-({$branch}{$hash})";
            SemVersUtils::updateExtendedVersionFile($context->version_file_path, $bumpedSemVers, $branch, $hash);
        } else {
            $bumpedSemVers = $semvers->bump($bumpType);
            $hash = $git->getCommitHashForBranch($branch);
            $output->writeln("branch: {$branch} tag: {$semvers} hash: {$hash} -> bumped semvers: [{$bumpedSemVers}]");
            SemVersUtils::updateVersionFile($context->version_file_path, $bumpedSemVers);
            GitUtils::gitCommit();
            GitUtils::gitPush("origin", $branch);
            SemVersUtils::createTagFromSemVers($bumpedSemVers);
            SemVersUtils::pushSemversTag("origin", $bumpedSemVers);
            $versionFile = $context->version_file_path;
        }
        return 0;
	}
}
