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
}