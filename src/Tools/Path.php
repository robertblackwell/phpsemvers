<?php
namespace Tools;

use Symfony\Component\Finder\Finder;
class Path
{
    public static function join(...$bits)
    {
        return join("/", $bits);
    }

    public static function expandTilde(string $path): string
    {
        $home = getenv("HOME");
        $pp = str_replace('~', $home, $path);
        return $pp;
    }
}
class TTest {
    public static function fred(): string
    {
        return "I am fred";
    }
}

class Glob
{
    public static function findByExtension(string $dir, string $file_extension): array
    {
        $result = [];
        $finder = new Finder();
        $found = $finder->in($dir)->name("*.{$file_extension}");
        foreach ($found as $f) {
            $result[] = $f->getPathname();
        }
        return $result;
    }

    public static function pregScanDir(string $dirPath, string $pregPattern): array
    {
        return array_filter(scandir($dirPath), function ($p) use($pregPattern) {
            $x = preg_match($pregPattern, $p);
            return $x != 0;
        });

    }
}