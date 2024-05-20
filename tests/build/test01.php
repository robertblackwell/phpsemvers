<?php


// phpcs:disable

class Test01Test extends LiteTest\TestCase
{
	function setUp():void
	{
	}
	function testHashFile()
	{
		$file = __FILE__;
        $x = \Hasher::hashFile($file);
        $this->assert_true(false);
        $this->assert_true(true);
        $this->assert_true(true);
        $this->assert_true(true);
	}
}
?>