<?php
require_once(dirname(__FILE__)."/header.php");


class test_one extends UnittestCase{
	function setUp(){
	}
	public static function st($aparameter){
	    $a="thevariale";
	}
	function test_1(){return;
	    self::st("thisis the parameter");
	}
	function test_2(){return;
	}
	function test_3(){
	}
}
?>
