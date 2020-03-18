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

    protected array $data;//массив источник полученный из парсера, подразумевается что данные отсортированы по id
    protected int $lengthData;//длина массива посчитана один раз

    protected array $columns= [];
    protected array $groups= [];

    protected ColumnGroups $columnGroups;

    public function __construct(IParserDriver $parser)
    {
        $this->i= array_flip(self::FIELDS);
        $this->parser = $parser;

        $this->columnGroups= new ColumnGroups();
    }

    /**
     * Старт
     */
    public function exec(): void
    {
        $this->data= $this->parser->getData();
        $this->lengthData= count($this->data);//немного ускорить посчитав длину один раз

        $this->createDataColumns();//Создание вспомогательных массивов колонок, для удобного поиска email, card, phone


        for($i=0;$i<$this->lengthData;$i++)
        {
            $row= &$this->data[$i];
            $ids= $this->searchDublInColumns($row);

            if( count($ids) > 1 ){
                $searchIndexGroups= $this->columnGroups->makeGroup($ids);

                $this->groups[ $this->field($row, 'id') ]= $searchIndexGroups[0];

                $this->replaceGroupIndex($searchIndexGroups);
            }
        }
    }

    /**
     * Можно было бы визуализацию кинуть в отдельный класс, но мы экономим ресурсы, избегая лишнего перебора.
     * 1. Проход по исходнику и вспомогательному массиву groups, получение parent_id.
     * 2. Вывод на экран результата
     */
    public function render(): void
    {
        for($i=0;$i<$this->lengthData;$i++){

            $id= $this->field($this->data[$i], 'id');

            if( isset($this->groups[$id]) ){
                $newPid= min($this->columnGroups->getGroup( $this->groups[$id] ));
            }
            else{
                $newPid= $id;
            }

            echo $id.','.$newPid;
            if($i+1<$this->lengthData)echo "\r\n";//последний перевод строки не ставим
        }
    }

    /**
     * Заменить индексы всех групп кроме первой на индекс первой
     * @param array
     */
    protected function replaceGroupIndex($searchIndexGroups)
    {
        if( count($searchIndexGroups) < 2 )return;

        foreach (array_slice($searchIndexGroups, 1) as $otherGroup){

            $found= array_keys($this->groups, $otherGroup);
            if(!$found)continue;

            foreach($found as $id){
                $this->groups[$id]= $searchIndexGroups[0];
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

    /**
     * Создание вспомоготельных массивов колонок для последующего поиска по ним. email|card|phone find row id
     */
    protected function createDataColumns(): void
    {
        foreach(self::GROUP_FIELDS as $fieldName){
            $this->columns[$fieldName]= array_column($this->data, $this->i[$fieldName], $this->i['id']);
        };
    }

    /**
     * Получить значение по ключу
     * @param array $data
     * @param string $name
     * @return mixed
     */
    protected function field(array $data, string $name)
    {
        return $data[$this->i[$name]];
    }
}