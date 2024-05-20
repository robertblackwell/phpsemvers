<?php

use Ctl\Commands\ConfigObject;
use Tools\EntityEnum;
use Tools\ProjectEnum;
use Tools\ServerEnum;

include_once dirname(dirname(__FILE__)).'/vendor/autoload.php';
function main()
{
    try {
        $cfg = new ConfigObject();
        $cfg->do_it(ServerEnum::from("opalstack"), ProjectEnum::from("prod"), "trip", "slug");
        var_dump($cfg);
        $r1 = $cfg->getDestinationFor(EntityEnum::from("gallery"));
        $r2 = $cfg->getSourceFor(EntityEnum::from("gallery"));

//$r3 = $cfg->getDestinationForAssetType("aaa", "prod");
//$r32= $cfg->getSourceForAssetType("aaa", "prod");

        $r4  = $cfg->getDestinationForOneEntity(ServerEnum::from("opalstack"), EntityEnum::from("item"), ProjectEnum::from("prod"), "trip", "slug");
        $r41 = $cfg->getDestinationForOneEntity(ServerEnum::from("opalstack"), EntityEnum::from("item"), ProjectEnum::from("prod"), "trip", "slug");

        $r5    = $cfg->getGpsDailyPath(ServerEnum::from("opalstack"), ProjectEnum::from("prod"),"trip", "slug");
        $r6    = $cfg->getGpsRawPath(ServerEnum::from("opalstack"), ProjectEnum::from("prod"), "trip", "slug");
        $r22   = ProjectEnum::choices();
        $en1   = ProjectEnum::Prod;
        $sen1  = "{$en1->value}";
        $s2en1 = "{$en1->name}";
        $n     = ProjectEnum::listNames();
        $v     = ProjectEnum::listValues();
        var_dump($cfg);
    } catch(\Exception $e) {
        var_dump($e);
    }
}
main();