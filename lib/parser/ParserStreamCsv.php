<?php namespace Lib\Parser;

/**
 * Парсер csv из входящего потока - вариант 1
 * Project: epicentrk
 * User: cooper
 * Date: 14.03.2020
 */

class ParserStreamCsv implements IParserDriver
{

    public function getData(): array
    {
        $data = array_map('str_getcsv', file('php://input'));
        return $data;
    }
}