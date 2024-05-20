<?php
ini_set("assert.exception", "1");

//require(dirname(__FILE__, 2) ."/vendor/autoload.php");
require(dirname(__FILE__, )."/include/header.php");

class TestCleanTest extends LiteTest\TestCase
{
    function cleancss(string $skinName): void
    {
        $fixture = new Context();
        $waRoot = $fixture->whiteacornRoot();
        $output = new StdOutput(Verbosity::DEBUG);
        $skin = SkinFactory::create($waRoot, $skinName);
        $skinDir = $skin->getSkinDir();

        system("cp {$skinDir}/dist-saved/*  {$skinDir}/dist/");
        $beforeFiles = $skin->cleanCssGlob();
        CSSHandler::cleanCss($output, $skin);
        $afterFiles = $skin->cleanCssGlob();
        $this->assert_true((count($afterFiles) == 0) && (count($beforeFiles) != 0));
    }

    function cleanjs(string $skinName): void
    {
        $fixture = new Context();
        $waRoot = $fixture->whiteacornRoot();
        $output = new StdOutput(Verbosity::DEBUG);
        $skin = SkinFactory::create($waRoot, $skinName);
        $skinDir = $skin->getSkinDir();

        system("cp {$skinDir}/dist-saved/*  {$skinDir}/dist/");
        $beforeFiles = $skin->cleanJsGlob();
        JSHandler::cleanJs($output, $skin);
        $afterFiles = $skin->cleanJsGlob();
        $this->assert_true((count($afterFiles) == 0) && (count($beforeFiles) != 0));
    }
    function testcleanjs()
    {
        $this->cleanjs("ctl");
        $this->cleanjs("rtw");
        $this->cleanjs("theamericas");
    }
    function testcleancss()
    {
        $this->cleancss("ctl");
        $this->cleancss("rtw");
        $this->cleancss("theamericas");
    }
}
