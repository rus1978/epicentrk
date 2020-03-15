<?php namespace Epicentrk;

require realpath(__DIR__.'/../vendor/autoload.php');

set_time_limit (0);


//Вариант: чтение данных из файла в формате csv
$parser= new \Lib\Parser\ParserFileCsv(__DIR__.'/files/users.csv');

//Вариант: чтение данных из входящего потока в фомате csv
//$parser= new \Lib\Parser\ParserStreamCsv();
//$parser= new \Lib\Parser\ParserStreamCsv2();



$app = new \Lib\Parser\DuplicateSearch($parser);
$app->exec();
$app->render();