<?php
namespace Tools;

function pexec(string $cmd): bool
{
    ob_implicit_flush(true);
//    ob_end_flush();
    $descriptorspec = [
        0 => ["pipe", "r"],   // stdin is a pipe that the child will read from
        1 => ["pipe", "w"],   // stdout is a pipe that the child will write to
        2 => ["pipe", "w"]    // stderr is a pipe that the child will write to
    ];
    $process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());
    if (is_resource($process)) {
        while ($s = fgets($pipes[1])) {
            print $s;
        }
        return true;
    } else {
        return false;
    }
}
/**
 * A config object for pushing whiteacorn content from the local to the remote server using rsync
 */
class Rsync
{
	public function __construct()
	{
	}
    public function createRemoteDir(string $user, string $host, string $dirpath): void
    {
        passthru("ssh {$user}@{$host} mkdir -p {$dirpath}");
    }
    public function setPermissionAll(string $user, string $host, string $perms, string $topDir): void
    {
        passthru("ssh {$user}@{$host} find ${$topDir} -type d chmod {$perms} {} \;");
    }
    /**
	 * Synchronize a local directory to a remote directory. 
	 * In a way that does not transmit apples .DS_Store. 
	 * Assumes that the remote server knows the local systems ssh key
	 * and password does have to be provided
	 * @param string $user The user name on the remote system
	 * @param string $host The remote servers domain name
	 * @param string $local_dirpath 		Full path of dir on local system that is to 
	 * 										be copied to the remote system
	 * @param string $remote_parent_dirpath The path of the parent directory on the 
	 * 										remote system
	 */
	public function syncRemoteDir(
		string $user, 
		string $host, 
		string $local_parent_dirpath, 
		string $remote_parent_dirpath,
		string $dirname,
		string $extra_options="") 
	{
		$source = "{$local_parent_dirpath}/{$dirname}";
        $remote_parent_dirpath = str_replace("//", "/", $remote_parent_dirpath . "/");
		$dest = "{$user}@{$host}:{$remote_parent_dirpath}";
		$opt_string = "--exclude='.DS_Store'";
        $rsync_path = "mkdir -p {$remote_parent_dirpath}";
        passthru("ssh {$user}@{$host} mkdir -p {$remote_parent_dirpath}");
		$cmd = "rsync -arv --delete --progress --perms {$extra_options} {$source} {$dest} ";
		print("command is : " . $cmd);
		$c = passthru($cmd);
		return $c;
	}
	/**
	 * @param string $source THe source from which the asset comes.
	 * @param string $destination Destination.
	 * @param string $options rsync options
	 * @param string $output_text
	 * @return int
	 */
	public function rsync_command(string $source, string $destination, string $options, string &$output_text): int
	{
		$destination = dirname($destination);
		$dest = $this->user."@".$this->url.":".$destination;
        $cmd = "rsync -arv --delete --perms --progress {$options} ". "$source $dest ";
        $retcode = 0;
		$c = passthru($cmd, $retcode);
        if($c === false)
            $output_text = "command failed retcode = {$retcode} here is the output \n";
//        else
//            $output_text = $c;
        return $retcode;
	}
}
//function main() {
//	$rsync = new Rsync();
//	$src_parent = "/Users/robertblackwell/Sites/whiteacorn";
//	$dest_parent ="/home/rob/apps/iracoon/whiteacorn";
//	$dirname = "php";
//	$rsync->syncRemoteDir("rob","opal14.opalstack.com", $src_parent, $dest_parent, $dirname, "-n");
//}
//main();
