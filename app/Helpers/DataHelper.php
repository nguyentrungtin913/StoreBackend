<?php


namespace App\Helpers;


use Illuminate\Database\Query\Builder;
use League\Fractal\TransformerAbstract;

class DataHelper
{
    /**
     * @param Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param TransformerAbstract $transformer
     * @param integer $perPage
     * @param string $name
     * @return array
     */
    static function getList($query, $transformer, $perPage, $name)
    {
        $data = [] ;
        $perPage = intval($perPage);
        if (!$perPage) {
            $data[$name] = $transformer->transformCollection($query->get());
        } else {
            $paginator = $query->paginate($perPage);
            $paginatorData = $transformer->paginate($paginator);
            $data[$name] = $paginatorData['data'];
            $data['pagination'] = $paginatorData['meta']['pagination'] ?? null;
        }
        return $data;
    }
    static public function sortData($arr, $by, $type)
    {
       for ($i=0; $i < count($arr) ; $i++) { 
           for ($j=0; $j < count($arr) ; $j++) { 
               if($type === 'desc') //giam
               {
                if($arr[$i][$by] < $arr[$j][$by])
                {
                    $temp = $arr[$i];
                    $arr[$i] = $arr[$j];
                    $arr[$j] = $temp;
                }
               }else{
                //tang
                if($arr[$i][$by] > $arr[$j][$by])
                {
                    $temp = $arr[$i];
                    $arr[$i] = $arr[$j];
                    $arr[$j] = $temp;
                }
               }
           }
       }
       return $arr;
    }
}