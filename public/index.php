<?php

require realpath(__DIR__.'/../vendor/autoload.php');

set_time_limit (0);


//todo настроить debugger, сортировка по id обязательна в начале



//Вариант: чтение данных из файла в формате csv
$parser= new Lib\Parser\ParserFileCsv(__DIR__.'/files/users.csv');

//Вариант: чтение данных из входящего потока в фомате csv
//$parser= new Lib\Parser\ParserStreamCsv();
$app = new Lib\Parser\DuplicateSearch($parser);


//render
foreach($app->exec() as $row){
    echo implode(',', $row)."\r\n";
}
