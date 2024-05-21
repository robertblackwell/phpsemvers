<?php
namespace Tools;
use \Tools\Path;

class Context
{
    public string $version_file_path;
    public static string $config_file_relpath = "php_semvers.json";

    private function __construct()
    {
    }

    private static function check_config_keys(\stdClass $config)
    {
        $required_keys = ["version", "version_file_relpath"];
        foreach ($required_keys as $k) {
            if (!property_exists($config, $k)) {
                throw new \Exception("Required key {$k} does not exist in the config file");
            };
        }
    }

    public static function create_from_config_file(): Context
    {
        $cwd = getcwd();
        $configFilePath = Path::join($cwd, self::$config_file_relpath);
        if (!is_file($configFilePath)) {
            throw new \Exception("could not find config file {$configFilePath} this is probably not a whiteacorn repo root ");
        }
        $gitDirRelPath = Path::join($cwd, ".git");
        if (!is_dir($gitDirRelPath)) {
            throw new \Exception("current dir is not a git repo - no .git subdir");
        }
        $file_config = json_decode(file_get_contents($configFilePath));
        if (is_null($file_config)) {
            throw new \Exception("{$configFilePath} is not a valid json file decode failed");
        }
        self::check_config_keys($file_config);
        $context = new Context();
        $context->current_version = $file_config->version;
        $context->version_file_path = Path::join($cwd, $file_config->version_file_relpath);
        return $context;
    }
}
class Checks {
/**
     * Check to see that the git repo that is cwd is clean
     * @return void
     * @throws \Exception
     */
    public static function checkGitRepoIsClean(): void
    {
        $cwd = getcwd();
        $git = new GitUtils($cwd);
        if (!$git->isClean()) {
            throw new \Exception("repo {$cwd} is not clean");
        }
    }

}
//class Checks
//{
//    /**
//     * Check the cwd is a repo containing an instance of the whiteacorn website.
//     * @return Object the whiteacorn config object
//     * @throws \Exception
//     */
//    public static function checkCwdIsWhiteacornRepo(): Object
//    {
//        $cwd = getcwd();
//        $configFilePath = Path::join($cwd, "whiteacorn.config.json");
//        if(! is_file($configFilePath)) {
//            throw new \Exception("could not find config file {$configFilePath} this is probably not a whiteacorn repo root ");
//        }
//        $config = json_decode(file_get_contents($configFilePath));
//        if(! in_array(basename($cwd), $config->valid_repo_dirnames)) {
//            $dirNames = join(", ", $config->valid_repo_names);
//            throw new \Exception("invalid current directory cwd is:[{$cwd}] the dirname must be one of [{$dirNames}]");
//        }
//        if(!is_dir(Path::join($cwd, ".git"))) {
//            throw new \Exception("current directory must be a git repo");
//        }
//        if(!is_dir(Path::join($cwd, "data"))) {
//            throw new \Exception("current directory does not have a data subdir");
//        }
//        $config->root_dir = $cwd;
//        $config->data_dir = Path::join($cwd, "data");
//        self::check_config_keys($config);
//
//        \Database\Locator::init($config);
//        $loc = \Database\Locator::get_instance();
//
//        return $config;
//    }
//    public static function check_config_keys(\stdClass $config)
//    {
//        $required_keys = ["valid_repo_dirnames", "version_file_relpath", "remote_servers", "deploy_list"];
//        foreach($required_keys as $k) {
//            if( ! property_exists($config, $k)){
//                throw new \Exception("Required key {$k} does not exist in the config file");
//            };
//        }
//    }
//    /**
//     * Check to see that the git repo that is cwd is clean
//     * @return void
//     * @throws \Exception
//     */
//    public static function checkGitRepoIsClean(): void
//    {
//        $cwd = getcwd();
//        $git = new GitUtils($cwd);
//        if (!$git->isClean()) {
//            throw new \Exception("repo {$cwd} is not clean");
//        }
//    }
//
//    /**
//     * Get the latest semver string from the git tags, the lastest commit hash
//     * and the currently active git branch. Combine these into a
//     * version signature and write that string to the version.php file
//     * whose path is given ????
//     * @return void
//     */
//    public static function updateVersionSignature(string $versionFilePath)
//    {
//
//    }
//}