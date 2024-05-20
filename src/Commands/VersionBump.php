<?php
namespace Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Tools\Context;
use Tools\Checks;
use Tools\SemVersUtils;
use Tools\SemVersBumpEnum;
use Tools\GitUtils;
use Tools\Path;

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
		$this
			->setName('v:bump')
			->setDescription(
				"Bumps, tag and commit the semantic version for a project git repo."
			)
			->setDefinition([
                new InputArgument('bump', InputArgument::REQUIRED, 'The part of the semver to bump major|minor|patch'),
                new InputOption('ignore-clean', "i", InputOption::VALUE_NONE,'Do not perform the git repo clean test'),
                new InputOption('dryrun', "d", InputOption::VALUE_NONE,'Do not create the new tag and dont push the tag'),
                new InputOption('save', "s", InputOption::VALUE_NONE,'Save the new version to the version file')
            ])
			->setHelp(
				'The <info>%command.name%</info> '
				.'Bumps the current semvers. Current semvers is stored as latest git tag'
			);
	}

    /**
     * @param InputInterface $input Input object.
     * @param OutputInterface $output Output object.
     * @return int
     * @throws \Exception
     */
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
//        $this->whiteacornConfig = Checks::checkCwdIsWhiteacornRepo();
        $context = Context::create_from_config_file();
        $cwd = getcwd();
        $clean_test = $input->getOption("ignore-clean");
        $dryrun = $input->getOption("dryrun");
        $save = $input->getOption("save");
        if(! $clean_test) {
            Checks::checkGitRepoIsClean();
        }
        $bumpTypeString = $input->getArgument("bump");
        $bumpType = SemVersBumpEnum::tryFrom($bumpTypeString);
        if(!$bumpType) {
            throw new \Exception("{$bumpTypeString} is an invalid bump operation use one of 'major|minor|patch'");
        }
        $git = new GitUtils($cwd);
        $branch = $git->getActiveBranch();
        $semvers = SemVersUtils::semversFromGitTag();
        $bumpedSemVers = $semvers->bump($bumpType);
        $hash = $git->getCommitHashForBranch($branch);
        if($dryrun) {
            $output->writeln("<fg=yellow>DRYRUN</> branch: {$branch} tag: {$semvers} hash: {$hash} -> bumped semvers: [{$bumpedSemVers}]");
        } else {
            $output->writeln("branch: {$branch} tag: {$semvers} hash: {$hash} -> bumped semvers: [{$bumpedSemVers}]");
            SemVersUtils::createTagFromSemVers($bumpedSemVers);
            SemVersUtils::pushSemversTag("origin", $bumpedSemVers);
            if($save) {
                $versionFile = Path::join($cwd, $this->whiteacornConfig->version_file_relpath);
                SemVersUtils::updateVersionFile($versionFile);
                $output->writeln("write {file_get_contents($versionFile)} to {$versionFile}");
            }
        }
		return 0;
	}
}
