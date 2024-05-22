<?php 
namespace PhpSemvers\Tools;
use Exception;
class GitUtils {
    private string $repo_root_dirpath;
    public function __construct(string $repo_root_dirpath) {
        $this->repo_root_dirpath = $repo_root_dirpath;
    }
    /**
     * Tests to see if there are uncommited changes.
     * Returns true if clean
     * git status --porcelain 
     * outputs nothing when there are no uncommitted changes
     * @return boolean 
     */
    public static function isClean(): bool {
        $outputText  = [];
        $retVal = 0;
        $x = exec("git status --porcelain", $outputText, $retVal);
        $len = strlen(implode($outputText));
        return ($len == 0);
    
    }
    /**
     * Gets the active branch
     * @return string
     * @throws \Exception
     */
    public static function getActiveBranch(): string
    {
        $branches = [];
        $branch = exec("git branch", $branches, $retVal);
        foreach($branches as $str){
            if( $str[0] == '*' ){
                return substr($str, 2);
            }
        }
        throw new \Exception("could not find an active branch");
    }
    /**
     * Assumes all tags are of the form vmmm.mmm.mmm. 
     * Gets all tags and sorts then in reverse alpha order.
     * 
     */
    public function getVersionLatestTag(): string {
        if(realpath(getcwd()) != $this->repo_root_dirpath) {
            chdir($this->repo_root_dirpath);
        }
        $toint = function (string $s): int {
            return intval($s);
        };
        $tags = shell_exec('git tag --sort -version:refname');

        $tags    = explode(PHP_EOL, shell_exec('git tag --sort -version:refname'));
        $tag_str = $tags[0];
        return $tag_str;
    }

    /**
     * Gets the latest git hash of the active branch
     * @param string $branch
     * @return string
     */
    public static function getCommitHashForBranch(string $branch): string
    {
//        if(realpath(getcwd()) != $this->repo_root_dirpath) {
//            chdir($this->repo_root_dirpath);
//        }
        $hash = exec("git rev-parse  $branch");
        return $hash;
    }
    public function createNewVersionTag(\Tools\SemVers $version) {
        if(realpath(getcwd()) != $this->repo_root_dirpath) {
            chdir($this->repo_root_dirpath);
        }
        exec('git tag ' . version_to_string($version));
    }
    public function pushTag(string $branch, string $remote, \Tools\SemVers $version) {
        if(realpath(getcwd()) != $this->repo_root_dirpath) {
            chdir($this->repo_root_dirpath);
        }
        exec("git push {$remote} {$version}");
    }
}
