<?php
namespace Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Tools\Deployer;
use Tools\ServerFactory;
use Tools\Checks;
use Tools\SemVersUtils;
use Tools\SemVersBumpEnum;
use Tools\GitUtils;
use Tools\Path;

class VersionInit extends Command
{
    private Object $whiteacornConfig;
    private function helpText()
    {
        return <<<EOD
"Initialize a semvers config file optionally with a startiing <info>semvers</info>.\n".

EOD;

    }
    /** @return void */
	protected function configure(): void
	{
		parent::configure();
		$this
			->setName('v:init')
			->setDescription(
				"Create and initialize a semvers config file got a project git repo. "
			)
			->setDefinition([
                new InputArgument('semvers', InputArgument::OPTIONAL, 'Optionally an initial value for the semvers. Default <info>v0.0.1</info>'),
                new InputOption('ignore-clean', "i", InputOption::VALUE_NONE,'Do not perform the git repo clean test'),
                new InputOption('dryrun', "d", InputOption::VALUE_NONE,'Do not create the new tag and dont push the tag'),
                new InputOption('save', "s", InputOption::VALUE_NONE,'Save the new version to the version file')
            ])
			->setHelp(
				'The <info>version:init</info> creates a new semvers config file and optionally sets the semvers value. '
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
        $this->whiteacornConfig = Checks::checkCwdIsWhiteacornRepo();
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
