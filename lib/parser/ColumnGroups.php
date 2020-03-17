<?php namespace Lib\Parser;
/**
 * Project: epicentrk
 * User: cooper
 * Date: 17.03.2020
 */
class ColumnGroups
{
    public array $groups=[    //////////// todo изменить модификатор сделать вместо него публичнуб функцию
//       [50],
//       [50],
//       [50],
////       [50],
//        [1],
//        [2],
//        [5],
//        [20]
    ];

    public function append(array $ids) : array
    {
        $searchIndexGroups= [];//найдены индексы групп

        $idWithoutGroup= [];//входящие ids без групп
        $this->searchGroups($ids, $searchIndexGroups, $idWithoutGroup);



        if($idWithoutGroup){//Создание новой группы
            $searchIndexGroups[]= count($this->groups);//добавить индекс новой группы
            $this->groups[]= $idWithoutGroup;//данные в группу
        }

       // dd($idWithoutGroup, $searchIndexGroups, $this->groups);


//        if(count($searchIndexGroups) == 1/* && !$idWithoutGroup*/){//Найдена одна группа, вернуть ее индекс
//           // echo 'второе-'."\n";
//           // dd($this->groups, $searchIndexGroups[0] );
//            return $searchIndexGroups[0];
//        }
//        else{
//          //  echo 'третье-'."\n";
//           // dd($searchIndexGroups, $this->groups);
//           // $this->groups[]= ['2222'];
//            $this->mergeGroups($searchIndexGroups);
//            //$this->groups[]= ['3333'];
//           // dd( $this->groups );
//
//        }

        if( count($searchIndexGroups) > 1 ){
            $this->mergeGroups($searchIndexGroups);
        }


        return $searchIndexGroups;
    }

    protected function mergeGroups(array $searchIndexGroups): void
    {
        $firstGroup= &$this->groups[$searchIndexGroups[0]];

        foreach (array_slice($searchIndexGroups, 1) as $otherGroup){
            $firstGroup= array_merge(
                $firstGroup,
                $this->groups[$otherGroup]
            );
            unset( $this->groups[$otherGroup] );
        }
    }


    /**
     * Поиск всех групп, где имеются переданные id, также поиск id которых нет ни в одной из групп
     * @param $ids
     * @return array
     */
    protected function searchGroups($ids, &$searchGroups, &$idWithoutGroup): void
    {

        $idWithoutGroup= $ids;
        foreach($ids as $idsIndex=>$id)//for использовать нельзя, т.к. индексы во входящем массиве могут быть не предсказуемы 1,3,5,6...
        {
            foreach ($this->groups as $groupIndex=>$groupItems){
                $groupItems = $this->groups[$groupIndex];

                if (in_array($id, $groupItems)) {
                    $searchGroups[] = $groupIndex;
                    unset($idWithoutGroup[$idsIndex]);
                }
            }
        }
        $searchGroups= array_unique($searchGroups);
        ksort($searchGroups);
    }
}