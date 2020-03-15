<?php namespace Lib\Parser;

/**
 * Project: epicentrk
 * User: cooper
 * Date: 14.03.2020
 */

class ParserFileCsv implements IParserDriver
{
    protected $pathFile;

    public function __construct($pathFile)
    {
        $this->pathFile= $pathFile;
    }

    public function getData(): array
    {
        $data = array_map('str_getcsv', file($this->pathFile));
        return $data;
    }
}