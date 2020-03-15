<?php namespace Lib\Parser;

/**
 * Базовый класс
 * Project: epicentrk
 * User: cooper
 * Date: 14.03.2020
 */

class DuplicateSearch
{
    const FIELDS= ['id', 'parentId', 'email', 'card', 'phone'];//позиция ожидаемыех полей во входящих данных
    const GROUP_FIELDS= ['email', 'card', 'phone'];//искать дубликаты в этих полях

    protected IParserDriver $parser;//хранит ссылку на один из объектов парсера
    private array $indexFields;//индексы для метода getField()

    protected array $data;//массив полученный из парсера
    protected int $lengthData;//длина массива посчитана один раз

    public function __construct(IParserDriver $parser)
    {
        $this->indexFields= array_flip(self::FIELDS);
        $this->parser = $parser;
    }

    protected function getField(array $array, string $fieldName): int
    {
        return $array[$this->indexFields[$fieldName]];
    }

    public function exec(): array
    {
        $this->data= $this->parser->getData();
        $this->lengthData= count($this->data);//немного ускорить посчитав длину один раз

        $groups= [];
        foreach(self::GROUP_FIELDS as $fieldName){
            $groupColumn= new GroupColumn(
                array_column($this->data, $this->indexFields[$fieldName]),
                $this->lengthData
            );
            $groups[$fieldName]= $groupColumn->get($this->data);
        };
        print_r($groups);



       // print_r( $this->data );$fieldName

        return $this->data;
    }

//    protected function render(array $groups): array
//    {
//        $result= [];
//        foreach($this->data as $index=>$row){
//            $result[$index]= [
//                //$this->getField($row, 'id'),
//                //$this->getField($row, 'card'),
//                $row[0],
//                $row[2],
//                '*'
//            ];
//        }
//        return $result;
//    }
}