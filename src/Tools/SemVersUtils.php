<?php 
namespace Tools;
Class SemVersUtils
{
    /**
     * Make a version object from the latest git tag
     */
    public static function semversFromGitTag(): SemVers
    {
        $toint = function (string $s): int {
            return intval($s);
        };
        $tags = shell_exec('git tag --sort -version:refname');

        $tags = explode(PHP_EOL, shell_exec('git tag --sort -version:refname'));
        $tag_str = $tags[0];
        $tmp = str_replace('v', '', $tags[0]);
        $parts = explode('.', $tmp);
        $vs = array_map($toint, $parts);
        return new SemVers($vs[0], $vs[1], $vs[2]);
    }

    public static function createTagFromSemVers(Semvers $semvers): void
    {
        passthru("git tag {$semvers}");
    }

    public static function getGitHubRemote()
    {

    }

    public static function pushSemversTag(string $remote, Semvers $semvers): void
    {
        passthru("git push {$remote} {$semvers}");
    }

    /**
     * @throws \Exception
     */
    public static function updateVersionFile(string $versionFile): void
    {
        if (!is_file($versionFile)) {
            throw new \Exception("version file {$versionFile} does not exists");
        }
        $semVers = SemVersUtils::semversFromGitTag();
        $branch = GitUtils::getActiveBranch();
        $hash = GitUtils::getCommitHashForBranch($branch);
        $r = "{$semVers}-{$branch}({$hash})";
        $phpContent = "<?php\n \t \$cfg = '" . $r . "';\n?>\n";
        file_put_contents($versionFile, $phpContent);
    }
}