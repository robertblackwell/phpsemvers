<?php
ini_set('display_errors','1');
/**
* Load the correct autoload.php depending on what runtime environment we are in
*/
$debug = false;
$pharPath = \Phar::running();
if (strlen($pharPath) == 0) {
	/* NOT running from a phar */
	/* test first option - running from its own repo via runner.php*/
	$vendor_dir =  dirname(dirname(__FILE__))."/vendor";

	if (is_dir($vendor_dir)) {
		if ($debug) print "running from repo\n";
		$vinfo = new SplFileInfo($vendor_dir);
		if ($vinfo->getBasename() !== "vendor") {
			throw new \Exception("require vendor autoload is asking for wrong file {$vendor_dir}");
		}
	} else {
		/**
		second option running as a php file and a composer installed package
		the php file is a link from from the packages repo ./vendor/bin directory
		*/
		if ($debug) print "running as composer installed package \n";
		$vendor_dir = dirname(dirname(dirname(dirname(__FILE__))));
		$vinfo = new SplFileInfo($vendor_dir);
		if ($vinfo->getBasename() !== "vendor") {
			throw new \Exception("require vendor autoload is asking for wrong file {$vendor_dir}");
		}
	}
	require_once($vendor_dir."/autoload.php");
} else {
	/** running from phar - phar path name is $pharPath */
	if ($debug) print "Running from phar {$pharPath}\n";
	if ($debug) print "vendor should be " . dirname(dirname(__FILE__))."/vendor/autoload.php\n";
	require_once($pharPath."/vendor/autoload.php");
	// require dirname(dirname(__FILE__))."/vendor/autoload.php";
}
function get_version()
{
    require dirname(__FILE__)."/Version.php";
    return $cfg;
}
use Symfony\Component\Console\Application;

$application = new Application("php_semvers", get_version());
$application->add(new PhpSemvers\Commands\VersionBump());
$application->add(new PhpSemvers\Commands\VersionInit());

$application->run();
