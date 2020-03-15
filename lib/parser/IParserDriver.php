<?php namespace Lib\Parser;

/**
 * Project: epicentrk
 * User: cooper
 * Date: 14.03.2020
 */

interface IParserDriver
{
    public function getData(): array;
}