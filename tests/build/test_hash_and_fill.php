<?php
ini_set("assert.exception", "1");

//require(dirname(__FILE__, 2) ."/vendor/autoload.php");
require(dirname(__FILE__, )."/include/header.php");
class TestBuild extends LiteTest\TestCase
{
    function hash_and_fill(string $skinName)
    {
        $fixture = new Context();
        $waRoot = $fixture->whiteacornRoot();
        $output = new StdOutput(Verbosity::DEBUG);
        $skin = SkinFactory::create($waRoot, $skinName);
        $skinDir = $skin->getSkinDir();

        $jshash = Hasher::hashFile("{$skinDir}/dist-saved/bundle.js");
        $csshash = Hasher::hashFile("{$skinDir}/dist-saved/combined.css");
        CSSHandler::cleanCss($output, $skin);
        JSHandler::cleanJs($output, $skin);

        $nohashed_bundlejs = "{$skinDir}/dist/bundle.js";
        $nohashed_bundledjsmap = "{$skinDir}/dist/bundle.js.map";
        $nohashed_combinedcss = "{$skinDir}/dist/combined.css";
        $nohashed_combinedmini = "{$skinDir}/dist/combined.min.css";

        $hashed_bundlejs = "{$skinDir}/dist/bundle.{$jshash}.js";
        $hashed_bundledjsmap = "{$skinDir}/dist/bundle.{$jshash}.js.map";
        $hashed_combinedcss = "{$skinDir}/dist/combined.{$csshash}.css";
        $hashed_combinedmini = "{$skinDir}/dist/combined.minified.{$csshash}.css";

        system("cp -v {$skinDir}/dist-saved/bundle.js {$skinDir}/dist");
        system("cp -v {$skinDir}/dist-saved/bundle.js.map {$skinDir}/dist");
        system("cp -v {$skinDir}/dist-saved/combined.css {$skinDir}/dist");
        system("cp -v {$skinDir}/dist-saved/combined.min.css {$skinDir}/dist");

        $this->assert_true(!is_file($hashed_bundlejs));
        $this->assert_true(!is_file($hashed_bundledjsmap));
        $this->assert_true(!is_file($hashed_combinedcss));
        $this->assert_true(!is_file($hashed_combinedmini));

        HashAndFill::hashJs($output, $skin);
        HashAndFill::hashCss($output, $skin);

        $this->assert_true(is_file($hashed_bundlejs));
        $this->assert_true(is_file($hashed_bundledjsmap));
        $this->assert_true(is_file($hashed_combinedcss));
        $this->assert_true(is_file($hashed_combinedmini));
    }

    function fill_indextemplate(string $skinName)
    {
        $fixture = new Context();
        $waRoot = $fixture->whiteacornRoot();
        $output = new StdOutput(Verbosity::DEBUG);
        $skin = SkinFactory::create($waRoot, $skinName);
        $skinDir = $skin->getSkinDir();

        $jshash = Hasher::hashFile("{$skinDir}/dist-saved/bundle.js");
        $csshash = Hasher::hashFile("{$skinDir}/dist-saved/combined.css");
        if (is_file($skin->getBaseFilePath())) {
            unlink($skin->getBaseFilePath());
        }
        $this->assert_true(!is_file($skin->getBaseFilePath()));
        $baseTemplatePath = $skin->getBaseTemplatePath();
        $templateContent = file_get_contents($skin->getBaseTemplatePath());
        $this->assert_true(str_contains($templateContent, "dist/bundle.js"));
        $this->assert_true(str_contains($templateContent, "dist/combined.css"));

        HashAndFill::fillPhpTemplate($output, $skin, $jshash, $csshash);

        $this->assert_true(is_file($skin->getBaseFilePath()));

        $indexFileContent = file_get_contents($skin->getBaseFilePath());

        $this->assert_true(!str_contains($indexFileContent, "dist/bundle.js"));
        $this->assert_true(!str_contains($indexFileContent, "dist/combined.css"));

        $this->assert_true(str_contains($indexFileContent, "dist/bundle.{$jshash}.js"));
        $this->assert_true(str_contains($indexFileContent, "dist/combined.{$csshash}.css"));

    }
    function test()
    {
        $this->fill_indextemplate("ctl");
        $this->hash_and_fill("ctl");
        $this->fill_indextemplate("rtw");
        $this->hash_and_fill("rtw");

    }
}
