<?php namespace Lib\Parser;

/**
 * Базовый класс
 * Project: epicentrk
 * User: cooper
 * Date: 14.03.2020
 */

class DuplicateSearch
{
    const FIELDS= ['id', 'parentId', 'email', 'card', 'phone'];//шаблон/позиция ожидаемыех полей во входящих данных
    const GROUP_FIELDS= ['email', 'card', 'phone'];//искать дубликаты в этих полях

    protected IParserDriver $parser;//хранит ссылку на один из объектов парсера (паттерн стратегия)
    protected array $i;//вспомогательный массив, позволяет получить индекс по имени поля
   // protected array $groups= [];

    protected array $data;//массив источник полученный из парсера, подразумевается что данные отсортированы по id
    protected int $lengthData;//длина массива посчитана один раз

    protected array $columns= [];
    protected array $groupToId= [];

    public function __construct(IParserDriver $parser)
    {
        $this->i= array_flip(self::FIELDS);
        $this->parser = $parser;
    }


    protected function field(array $data, string $name)
    {
        return $data[$this->i[$name]];
    }

    /**
     * Собственно старт
     */
    public function exec(): void
    {
        $this->data= $this->parser->getData();
        $this->lengthData= count($this->data);//немного ускорить посчитав длину один раз

        $this->createDataColumns();//Создание вспомогательных массивов колонок, для удобного поиска email, card, phone

        $columnGroups= new ColumnGroups();

        for($i=0;$i<$this->lengthData;$i++)
        {
            $row= &$this->data[$i];
            $ids= $this->searchDublInColumns($row);

            if( count($ids) > 1 ){
                $searchIndexGroups= $columnGroups->makeGroup($ids);

                $this->groupToId[ $this->field($row, 'id') ]= $searchIndexGroups[0];

                $this->replaceGroupIndex($searchIndexGroups);
            }
        }

        $this->render($columnGroups);
    }

    protected function replaceGroupIndex($searchIndexGroups)
    {
        if( count($searchIndexGroups) < 2 )return;

        foreach (array_slice($searchIndexGroups, 1) as $otherGroup){

            $founded= array_keys($this->groupToId, $otherGroup);

            if($founded){
                foreach($founded as $id){
                    $this->groupToId[$id]= $searchIndexGroups[0];
                }
            }
        }
    }

    /**
     * Поиск дубликатов по всем ключевым колонкам (email, card, phone)
     * @param array $row
     * @return array
     */
    protected function searchDublInColumns(array $row) : array
    {
        $keys= [];
        foreach(self::GROUP_FIELDS as $fieldName){
            $keys= array_merge( $keys, array_keys($this->columns[$fieldName], $this->field($row, $fieldName)) );
        }
        return array_unique($keys);
    }

    protected function createDataColumns(): void
    {
        foreach(self::GROUP_FIELDS as $fieldName){
            $this->columns[$fieldName]= array_column($this->data, $this->i[$fieldName], $this->i['id']);
        };
    }


    /**
     * Можно было бы визуализацию кинуть в отдельный класс, но мы экономим ресурсы, избегая лишнего перебора.
     * 1. Проход по исходнику и вспомогательному массиву связей, поиск и замена pid по всей глубине предков.
     * 2. Вывод на экран результата
     */
    public function render($columnGroups): void
    {
        for($i=0;$i<$this->lengthData;$i++){

            $id= $this->field($this->data[$i], 'id');
            $groupIndex= (isset($this->groupToId[$id]) ? $this->groupToId[$id] : '*');


            //$newPid= $columnGroups->groups[ $groupIndex ];

            echo $id.','.$groupIndex."\n";
            if($i+1<$this->lengthData)echo "\r\n";//последний перевод строки не ставим
        }

        print_r($columnGroups->groups);
    }
}