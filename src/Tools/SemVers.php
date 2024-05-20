<?php
namespace Tools;
class SemVers {
    const MAJOR = "major";
    const MINOR = "minor";
    const PATCH = "patch";
    public int $major;
    public int $minor;
    public int $patch;
    public function __construct(int $major, int $minor, int $patch)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
    }
    public static function isValidVersionString(string $vs): bool {
        $matches = [];
        $m2 = [];
        $y = preg_match("/v([0-9]+)\.([0-9]+)\.([0-9]+)/", $vs, $matches);
        $x = preg_match("/v(0|[1-9][0-9]*)\.([0-9]+)\.([0-9]+)$/", $vs, $m2);
        return ($x === 1);
    }
    public static function createFromString(string $vs): SemVers | null
    {
        $m = [];
        $x = preg_match("/v(0|[1-9][0-9]*)\.([0-9]+)\.([0-9]+)$/", $vs, $m2);
        if($x == 1) {
            return new SemVers(intval($m[1]), intval($m[2]), intval($m[3]));
        }
        return null;
    }

    public static function compare(SemVers $v1, SemVers $v2): int {
        if($v1->major != $v2->major) {
            return ($v1->major < $v2->major) ? -1 : 1;
        } else if($v1->minor != $v2->minor) {
            return ($v1->minor < $v2->minor) ? -1 : 1;
        } else {
            return ($v1->patch < $v2->patch) ? -1 : 1;
        }
        return 0;
    }
    public function isEqualTo(SemVers $other): bool {
        return (self::compare($this, $other) == 0) ;
    }
    public function __toString(): string
    {
        return "v{$this->major}.{$this->minor}.{$this->patch}";
    }
    function bumpMajor(): SemVers
    {
        return (new SemVers($this->major+1, 0, 0));
    }
    function bumpMinor(): SemVers
    {
        return (new SemVers($this->major, $this->minor+1, 0));
    }
    function bumpPatch(): SemVers
    {
        return (new SemVers($this->major, $this->minor, $this->patch+1));
    }

    function bump(SemVersBumpEnum $bump_type): SemVers
    {
        $r = null;
        switch ($bump_type) {
            case SemVersBumpEnum::MAJOR:
                $r = $this->bumpMajor();
                break;
            case SemVersBumpEnum::MINOR:
                $r = $this->bumpMinor();
                break;
            case SemVersBumpEnum::PATCH:
                $r = $this->bumpPatch();
                break;
            default:
                throw new \Exception("bump_version invalid bump type {$bump_type}");
        }
        return $r;
    }
}
