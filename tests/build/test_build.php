<?php
ini_set("assert.exception", "1");

//require(dirname(__FILE__, 2) ."/vendor/autoload.php");
require(dirname(__FILE__, )."/include/header.php");
class TestBuild extends LiteTest\TestCase
{
    function buildCss(string $skinName)
    {
        $fixture = new Context();
        $waRoot = $fixture->whiteacornRoot();
        $output = new StdOutput(Verbosity::DEBUG);
        $skin = SkinFactory::create($waRoot, $skinName);
        $skinDir = $skin->getSkinDir();

        CSSHandler::cleanCss($output, $skin);
        if(is_file($skin->getCssMasterPath())) {
            unlink($skin->getCssMasterPath());
        }
        $this->assert_true(!is_file($skin->getCssMasterPath()));
        $this->assert_true(!is_file($skin->getCssBundlePath()));
        $this->assert_true(!is_file($skin->getMinifiedCssBundlePath()));

        $beforeFiles = $skin->cleanCssGlob();
        CSSHandler::runLessc($output, $skin);
        $afterFiles = $skin->cleanCssGlob();
        $this->assert_true((count($afterFiles) == 0) && (count($beforeFiles) == 0) && (is_file($skin->getCssMasterPath())));

        CSSHandler::cssBeforeAndAfterFiles($output, $skin);
        $this->assert_true(is_file($skin->getCssBundlePath()));
        CSSHandler::cssMinify($output, $skin);
        $this->assert_true(is_file($skin->getMinifiedCssBundlePath()));

    }
    function buildjs(string $skinName)
    {
        $fixture = new Context();
        $waRoot = $fixture->whiteacornRoot();
        $output = new StdOutput(Verbosity::DEBUG);
        $skin = SkinFactory::create($waRoot, $skinName);
        $skinDir = $skin->getSkinDir();
        $cwd = getcwd();

        JSHandler::cleanJs($output, $skin);
        $beforeFiles = $skin->cleanJsGlob();
        JSHandler::runWebpack($output, $skin);
        $afterFiles = $skin->cleanJsGlob();
        $this->assert_true((count($afterFiles) == 2) && (count($beforeFiles) == 0));
    }
    function build(string $skinName)
    {
        $this->buildcss($skinName);
        $this->buildjs($skinName);
    }
    function test()
    {
        $this->build("ctl");
        $this->build("rtw");
        $this->build("theamericas");
    }
}
