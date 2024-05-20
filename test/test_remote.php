<?php

use Ctl\Commands\ConfigObject;
use Ctl\Commands\Remote;

include_once dirname(dirname(__FILE__)).'/vendor/autoload.php';
function test_get(): void
{
    $remote = new Remote();
    $res  = $remote->get("http://whiteacorn/test/json1", []);
    var_dump($res);
}
function test_get_json(): void
{
    $remote = new Remote();
    $res  = $remote->get_json("https://iracoon.com/run_cmd", "/item/show?slug=161030");
    var_dump($res);
}
function main()
{
    try {
        test_get_json();
    } catch(\Exception $e) {
        var_dump($e);
    }
}
main();