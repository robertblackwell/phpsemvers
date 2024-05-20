<?php
require_once(dirname(__FILE__)."/header.php");


class TestExplicitMethod extends UnittestCase{
	function setUp(){
	}
	function Test1(){
	}
	function Test2(){
	}
}
class TestExplicitClass extends UnittestCase{
	function setUp(){
	}
	function Test1(){
	}
	function Test2(){
	}
}
class TestExplicitClassX extends UnittestCase{
	function setUp(){
	}
	function Test1(){
	}
	function Test2(){
	}
}
class TestExplicitMethodX extends UnittestCase{
	function setUp(){
	}
	function Test1(){
	}
	function Test2(){
	}
}
function non_class_function($one, $two){
}
class TestExplicitFunctionX extends UnittestCase{
	function setUp(){
	}
	function Test2(){
	    non_class_function('111','222');
	}
}
class TestImpliedConstructor extends UnittestCase{
    function __construct(){
        parent::__construct();
    }
	function setUp(){
	}
	function Test2(){
	    non_class_function('111','222');
	}
}

?>
