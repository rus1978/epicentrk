<?php /*namespace Epicentrk*/;

require realpath(__DIR__.'/../vendor/autoload.php');

set_time_limit (0);


//Вариант: чтение данных из файла в формате csv
$parser= new \Lib\Parser\ParserFileCsv(__DIR__.'/files/users.csv');

//Вариант: чтение данных из входящего потока в фомате csv
//$parser= new \Lib\Parser\ParserStreamCsv();

function dd($data)
{
    foreach(func_get_args() as $value){
        var_export($value);
        echo "\n";
    }
    exit;
}

$app = new \Lib\Parser\DuplicateSearch($parser);
$app->exec();
//$app->render();