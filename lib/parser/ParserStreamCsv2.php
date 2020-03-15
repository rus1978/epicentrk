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
        $result= [];

        if(($handle = fopen('php://input', 'r')) !== FALSE) {
            while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $result[] = $data;
            }
            fclose($handle);
        }

        return $result;
    }
}