<?php
namespace Tools;
use Symfony\Component\Console\Output\OutputInterface;
class Deployer
{
    public static function deployOneDir(OutputInterface $output, ServerBase $server, string $dirName, string $localParentDirPath, string $remoteParentDirPath, bool $dryrun): void
    {
        $dryrunString = ($dryrun) ? "-n" : "";
        $extraOptions = $dryrunString;
        $output->writeln("deploy rsync {$dryrunString} {$localParentDirPath}/{$dirName} -> {$server->user}@{$server->host}:{$remoteParentDirPath}");
        $rsync = new Rsync();
        if(($server->apps_directory != $remoteParentDirPath) && (!$dryrun)) {
            $output->writeln("creating {$remoteParentDirPath}");
            $extraOptions = "--rsync-path=\"mkdir -p {$remoteParentDirPath} && rsync \" {$extraOptions}";
        }
        $rsync->syncRemoteDir($server->user, $server->host, $localParentDirPath, $remoteParentDirPath."/", $dirName, $extraOptions);
    }

    /**
     * @throws \Exception
     */
    public static function deployDirs(OutputInterface $output, string $fromRootDir, ServerBase $server, array $dirPaths, bool $dryrun): void
    {
        foreach($dirPaths as $dir) {
            $dirPath = Path::join($fromRootDir, $dir);
            if(!is_dir($dirPath)  && (!is_file($dirPath))) {
                throw new \Exception("cannot deploy dir {$dirPath} it does not exist");
            }
        }
        foreach($dirPaths as $dir) {
            $srcDirPath = Path::join($fromRootDir, $dir);
            $srcParentDir = dirname($srcDirPath);
            $file = basename($srcDirPath);
            $destDirPath = Path::join($server->apps_directory, $dir);
            $destParentDir = dirname($destDirPath);
            $output->writeln("<fg=magenta>deploy:</><fg=green>{$dir} <fg=magenta>from:</>{$srcParentDir} <fg=magenta>to:</>{$destParentDir}");
            self::deployOneDir($output, $server, $file, $srcParentDir, $destParentDir, $dryrun);
        }
    }

    /**
     * @throws \Exception
     */
    public static function deploySite(OutputInterface $output, string $fromRootDir, array $deployList, ServerBase $server, bool $dryrun): void
    {
        self::deployDirs($output, $fromRootDir, $server, $deployList, $dryrun);
    }
}