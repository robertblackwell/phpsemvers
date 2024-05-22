<?php
namespace PhpSemvers\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Tools\Deployer;
use PhpSemvers\Tools\SemVers;
use PhpSemvers\Tools\ServerFactory;
use PhpSemvers\Tools\Context;
use PhpSemvers\Tools\SemVersUtils;
use PhpSemvers\Tools\SemVersBumpEnum;
use PhpSemvers\Tools\GitUtils;
use PhpSemvers\Tools\Path;

class VersionInit extends Command
{
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
                new InputArgument("save_file", InputArgument::OPTIONAL,
                    "Rel path to php file where <info>php_semvers</info>should write the updated version number string. Default <info>./src/Version.php</info> "),
//                new InputArgument('semvers', InputArgument::OPTIONAL, 'Optionally an initial value for the semvers. Default <info>v0.0.1</info>'),
//                new InputOption('ignore-clean', "i", InputOption::VALUE_NONE,'Do not perform the git repo clean test'),
//                new InputOption('dryrun', "d", InputOption::VALUE_NONE,'Do not create the new tag and dont push the tag'),
//                new InputOption('save', "s", InputOption::VALUE_NONE,'Save the new version to the version file')
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
        $cwd = getcwd();
        $config_path = Path::join($cwd, Context::$config_file_relpath);
        if(is_file($config_path)) {
            $output->writeln("<fg=blue>Will overwrite config file</> <info>{$config_path}</info> <fg=blue>if you continue</>");
        } else {
            $output->writeln("<fg=blue>Will create Config file <info>{$config_path}</info> <fg=blue>if you continue</>");
        }
        $save_relpath = (!is_null($input->getArgument("save_file")) ? $input->getArgument("save_file"): "src/Version.php");
        $save_file_path = Path::join($cwd, $save_relpath);
        if(is_file($save_file_path)) {
            $output->writeln("<fg=blue>Will overwrite php version file <info>${save_relpath} </info><fg=blue>if you continue" );
        } else {
            $output->writeln("<fg=blue>Will create php version file <info>${save_relpath}</info> <fg=blue>if you continue" );
        }
        $git = new GitUtils($cwd);
        if(!is_dir(Path::join($cwd, ".git"))) {
            throw new \Exception("This does not look like a git repo. The current dir does not have a subdir called <info>.git</info>");
        }
        $branch = $git->getActiveBranch();
        $semvers = SemVersUtils::semversFromGitTag();
//        if(is_null($semvers)) {
//            $output->writeln("This git repo has no tags.");
//            $output->writeln("Will create a tag for <info>v0.0.1</info>");
//            $semvers = SemVers::createFromString("v0.0.1");
//            SemVersUtils::createTagFromSemVers($semvers);
//        }
        $versionFile = $save_file_path;
        $vfc = SemVersUtils::createVersionFileContent();
        $json = <<<EOD
    {
        "version_file_relpath":"src/Version.php"
    }
EOD;
        $new_semvers = null;
        if(is_null($semvers)) {
            $output->writeln("<fg=blue>Will create and push a tag</> <info>v0.0.1</info> <fg=blue>if you continue</>");
            $new_semvers = SemVers::createFromString("v0.0.1");
            $output->writeln("<fg=blue>Will push the tag</> <info>{$new_semvers}</info>"
                ." <fg=blue>with</> <info>git push origin --tag</info> <fg=blue>if you continue</>");
        } else {
            $output->writeln("<fg=blue>Will push git tag</> <info>{$semvers}</info>"
                ." <fg=blue>and run</> <info>git push origin --tag</info> <fg=blue>if you continue</>");
        }
//        $output->writeln("Will write {$vfc} to file {$versionFile}if you continue");
//        $output->writeln("Will write  into file {$config_path} if you continue");
        $yesNo = readline("Proceed ? ");
        if($yesNo == "Y" || $yesNo == "y") {
            if(is_null($semvers)) {
                $semvers = $new_semvers;
            }
            SemVersUtils::pushSemversTag("origin", $semvers);
            file_put_contents($config_path, $json);
            file_put_contents($save_file_path, $vfc);
        }
        return 0;
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
