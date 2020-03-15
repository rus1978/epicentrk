<?php namespace Lib\Parser;

/**
 * Project: epicentrk
 * User: cooper
 * Date: 14.03.2020
 */

class GroupColumn
{
    //protected array $dataColumn;
    protected int $lengthData;
    protected array $data;
    protected int $i; //индекс группы

    public function __construct(array $data, int $groupIndex, int $lengthData)
    {
        $this->data= $data;
        $this->i= $groupIndex;
        $this->lengthData= $lengthData;

        $this->setSortData();



        //$columns= array_column($this->data, $groupIndex, 0 );

        //print_r($this->data);
        //exit;

 //       $this->dataColumn= $data;
 //       asort($this->dataColumn);//вся логика основана на том, что входищие данные уже отсортированы

//        print_r($this->dataColumn);
//        exit;


    }

    /**
     * Сортировка по выбранному полю и по индексу
     * @param $groupIndex
     */
    protected function setSortData()
    {
        usort($this->data, function ($a, $b)
        {
            $retval = strnatcmp($a[$this->i], $b[$this->i]); //email | phone | card
            if(!$retval) $retval = strnatcmp($a[0], $b[0]);// id field
            return $retval;
        });
    }

    public function exec(array &$groups, array &$groups2): void
    {

        $groupMinIndex= $this->lengthData+1;//заведомо большое значение которого не может быть в качестве индекса в массиве данных. Использую дабы не плодить флаги

        do{
            $current= current($this->data);
            $currentValue= $current[$this->i];
            $currentId= $current[0];

            $result= &$groups[$currentId];
            $result['id']= $currentId;
            $result['val']= $currentValue;

            $pid= &$groups2[$currentId];

            $next= next($this->data);
            $nextValue= $next[$this->i];

            if( !isset($result['pid']) ){
                $result['pid']= $currentId;
                $pid= $currentId;
            }

            if($currentValue == $nextValue){
                if($groupMinIndex==$this->lengthData+1)$groupMinIndex= $currentId;//единожды при создании группы
                $result['pid']= min($result['pid'], $groupMinIndex);
                $pid=  min($pid, $groupMinIndex);
            }
            else{
                if(isset($prevValue) && $currentValue == $prevValue){//тот случай, когда последний элемент в группе или в данных в целом
                    $result['pid']= min($result['pid'], $groupMinIndex);
                    $pid= min($pid, $groupMinIndex);
                }

                $groupMinIndex= $this->lengthData+1;//закрытие группы
            }

            $prevValue= $currentValue;
        }
        while ($next!==false);
    }
//
//    public function get(array &$originData): array
//    {
//        $groupMinIndex= $this->lengthData+1;//заведомо большое значение которого не может быть в качестве индекса в массиве данных. Использую дабы не плодить флаги
//        $result= [];
//
//        for($i=0; $i<$this->lengthData; $i++){
//
//            $currentValue= current($this->dataColumn);
//            $currentIndex= key($this->dataColumn);
//
//            $currentRow= &$originData[$currentIndex];
//            $currentId= &$currentRow[0];
//            $currentPID= &$currentRow[1];
//
//
//            $nextValue= next($this->dataColumn);
//
//
//            if( $currentPID === 'NULL' )$currentPID= $currentId;
//
//            if($currentValue == $nextValue){
//                if($groupMinIndex==$this->lengthData+1)$groupMinIndex= $currentId;//единожды при создании группы
//                $result[$currentId]= $groupMinIndex;
//                $currentPID= min($currentPID, $groupMinIndex);
//            }
//            else{
//                if(isset($prevValue) && $currentValue == $prevValue){//тот случай, когда последний элемент в группе или в данных в целом
//                    $result[$currentId]= $groupMinIndex;
//                    $currentPID= min($currentPID, $groupMinIndex);
//                }
//
//                $groupMinIndex= $this->lengthData+1;//закрытие группы
//            }
//
//            $prevValue= $currentValue;
//        }
//        return $result;
//    }
}