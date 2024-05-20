<?php
namespace Deploy\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputOption;
use Tools\SemVersUtils;
use Tools\Checks;
use Tools\GitUtils;
class VersionSave extends Command
{
    private Object $whiteacornConfig;

    /** @return void */
	protected function configure(): void
	{
		parent::configure();
		$this
			->setName('version:save')
			->setDescription(
				'Create a version signature from the latest git tag and save to whiteacorn websites version file'
			)
			->setDefinition([
                new InputOption('ignore-clean', "i", InputOption::VALUE_NONE,'Do not perform the git repo clean test'),
                new InputOption('dryrun', "d", InputOption::VALUE_NONE,'Do not create the new tag and dont push the tag'),
            ])
			->setHelp(
				'The <info>%command.name%</info> '
				.'Create a version string from the latest semvers git tag and save it to the whiteacorn version file'
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
        $this->whiteacornConfig = Checks::checkCwdIsWhiteacornRepo();
        $clean_test = $input->getOption("ignore-clean");
        if(! $clean_test) {
            Checks::checkGitRepoIsClean();
        }
        $git = new GitUtils($cwd);
        $versionFile = $this->whiteacornConfig->version_file_relpath;
        SemVersUtils::updateVersionFile($versionFile);
        $output->writeln("write {file_get_contents($versionFile)} to {$versionFile}");
		return 0;
	}
}
