<?php namespace Lib\Parser;

/**
 * Project: epicentrk
 * User: cooper
 * Date: 14.03.2020
 */

class GroupColumn
{
    protected array $data;
    protected int $lengthData;

    public function __construct(array $data, int $lengthData)
    {
        $this->data= $data;
        asort($this->data);//вся логика основана на том, что входищие данные уже отсортированы

//        print_r($this->data);
//        exit;

        $this->lengthData= $lengthData;
    }

    public function get(array &$originData): array
    {
        $groupMinIndex= $this->lengthData+1;//заведомо большое значение которого не может быть в качестве индекса в массиве данных. Использую дабы не плодить флаги
        $result= [];

        for($i=0; $i<$this->lengthData; $i++){

            $currentValue= current($this->data);
            $currentIndex= key($this->data);

            $currentRow= &$originData[$currentIndex];
            $currentId= &$currentRow[0];
            $currentPID= &$currentRow[1];


            $nextValue= next($this->data);


            if( $currentPID === 'NULL' )$currentPID= $currentId;

            if($currentValue == $nextValue){
                if($groupMinIndex==$this->lengthData+1)$groupMinIndex= $currentId;//единожды при создании группы
                $result[$currentId]= $groupMinIndex;
                $currentPID= min($currentPID, $groupMinIndex);
            }
            else{
                if(isset($prevValue) && $currentValue == $prevValue){//тот случай, когда последний элемент в группе или в данных в целом
                    $result[$currentId]= $groupMinIndex;
                    $currentPID= min($currentPID, $groupMinIndex);
                }

                $groupMinIndex= $this->lengthData+1;//закрытие группы
            }

            $prevValue= $currentValue;
        }
        return $result;
    }
}