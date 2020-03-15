<?php namespace Lib\Parser;

/**
 * Парсер csv из входящего потока - вариант 2
 * Project: epicentrk
 * User: cooper
 * Date: 14.03.2020
 */

class ParserStreamCsv2 implements IParserDriver
{

    public function getData(): array
    {
        $data= [];
        $handle = fopen('php://input', "r");
        foreach (fgetcsv($handle, 1000, ",") as $row ){
            $data[]= $row;
        }
        fclose($handle);

        return $data;
    }
}