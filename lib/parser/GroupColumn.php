<?php namespace Lib\Parser;

/**
 * Project: epicentrk
 * User: cooper
 * Date: 14.03.2020
 */

class GroupColumn
{
    protected int $lengthData;
    protected array $data;
    protected int $groupIndex; //индекс группы

    public function __construct(array $data, int $groupIndex, int $idIndex, int $lengthData)
    {
        $this->data= $data;
        $this->groupIndex= $groupIndex;
        $this->idIndex= $idIndex;
        $this->lengthData= $lengthData;

        $this->setSortData();
    }

    /**
     * Сортировка по выбранному полю и по индексу (ASC)
     * @param $groupIndex
     */
    protected function setSortData()
    {
        usort($this->data, function ($a, $b)
        {
            $retval = strnatcmp($a[$this->groupIndex], $b[$this->groupIndex]); //email | phone | card
            if(!$retval) $retval = strnatcmp($a[$this->idIndex], $b[$this->idIndex]);// id field
            return $retval;
        });
    }

    /**
     *
     * @param array $groups
     */
    public function merge(array &$groups): void
    {
        $groupMinIndex= $this->lengthData+1;//заведомо большое значение которого не может быть в качестве индекса в массиве данных. Использую дабы не плодить флаги

        do{
            $current= current($this->data);
            $currentValue= $current[$this->groupIndex];
            $currentId= $current[$this->idIndex];

            $pid= &$groups[$currentId];

            $next= next($this->data);
            $nextValue= ($next === false) ? null : $next[$this->groupIndex];

            if(!isset($pid))$pid= $currentId;

            if($currentValue == $nextValue){
                if($groupMinIndex==$this->lengthData+1)$groupMinIndex= $currentId;//единожды при создании группы
                $pid= min($pid, $groupMinIndex);
            }
            else{
                if(isset($prevValue) && $currentValue == $prevValue){//тот случай, когда последний элемент в группе или в данных в целом
                    $pid= min($pid, $groupMinIndex);
                }

                $groupMinIndex= $this->lengthData+1;//закрытие группы - присвоив заведомо большое значение
            }

            $prevValue= $currentValue;
        }
        while ($next!==false);
    }
}