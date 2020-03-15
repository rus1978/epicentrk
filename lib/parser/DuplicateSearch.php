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
    protected array $groups= [];//массив связей id и pid

    protected array $data;//массив источник полученный из парсера, подразумевается что данные отсортированы по id
    protected int $lengthData;//длина массива посчитана один раз

    public function __construct(IParserDriver $parser)
    {
        $this->i= array_flip(self::FIELDS);
        $this->parser = $parser;
    }

    /**
     * Глубокий поиск предка
     * @param $pid
     * @return mixed
     */
    protected function getPidOrigin($pid): int
    {
        while($pid !== ($childPid=$this->groups[$pid]) ){
            $pid= $this->groups[$childPid];
        }
        return $pid;
    }

    /**
     * Собственно старт
     */
    public function exec(): void
    {
        $this->data= $this->parser->getData();
        $this->lengthData= count($this->data);//немного ускорить посчитав длину один раз

        //Проход по колонкам, фиксация групп
        foreach(self::GROUP_FIELDS as $fieldName){
            $groupColumn= new GroupColumn(
                $this->data,
                $this->i[$fieldName],
                $this->i['id'],
                $this->lengthData
            );
            $groupColumn->merge($this->groups);
        };
    }

    /**
     * Можно было бы визуализацию кинуть в отдельный класс, но мы экономим ресурсы, избегая лишнего перебора.
     * 1. Проход по исходнику и вспомогательному массиву связей, поиск и замена pid по всей глубине предков.
     * 2. Вывод на экран результата
     */
    public function render(): void
    {
        for($i=0;$i<$this->lengthData;$i++){
            $row= $this->data[$i];

            $id= $row[$this->i['id']];
            $newPid= $this->getPidOrigin($this->groups[$id]);//глубокий поиск родителя

            echo $id,','.$newPid;
            if($i+1<$this->lengthData)echo "\r\n";//последний перевод строки не ставим
        }
    }
}