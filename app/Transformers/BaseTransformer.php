<?php


namespace App\Transformers;


use App\Models\BaseModel;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Manager;
use Illuminate\Support\Arr;
use League\Fractal\TransformerAbstract;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Underscore\Parse;
use Underscore\Types\Strings;

abstract class BaseTransformer extends TransformerAbstract
{
    protected $fractal;
    protected $model;

    public function __construct(Manager $fractal, BaseModel $model)
    {
        $this->model = $model;
        $this->fractal = $fractal;
    }

    public function transformItem($data)
    {
        return $data ? $this->fractal->createData(new Item($data, $this))->toArray()['data'] ?? null : $data;
    }

    /**
     * @param $data
     * @return array
     */
    public function transformCollection($data)
    {
        return $data ? $this->fractal->createData(new Collection($data, $this))->toArray()['data'] ?? null : $data;
    }
     public function transform($data)
    {
        $return = [];
        $formattedData = is_array($data) ? $data : $data->toArray();


        foreach ($this->model::ALIAS as $field => $alias)
        {
            $value = Arr::get($formattedData, $field);
            Arr::set($return, $alias, $value);
        }

        $original = $return;

        $return['_original'] = $original;

        return $return;
    }
    public function paginate(LengthAwarePaginator $paginator)
    {
        $resource = new Collection($paginator->items(), $this);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        $data = $this->fractal->createData($resource)->toArray() ?? null;
        //Convert pagination to camel style
        if ($data['meta']['pagination']) {
            foreach ($data['meta']['pagination'] as $key => $val) {
                if ($key !== Strings::toCamelCase($key)) {
                    $data['meta']['pagination'][Strings::toCamelCase($key)] = $val;
                    unset($data['meta']['pagination'][$key]);
                }

                if ($key == 'links') {
                    foreach ($data['meta']['pagination'][$key] as $key2 => $val2) {
                        if ($key2 !== Strings::toCamelCase($key2)) {
                            $data['meta']['pagination'][$key][Strings::toCamelCase($key2)] = $val2;
                            unset($data['meta']['pagination'][$key][$key2]);
                        }
                    }
                }
            }
        }
        return $data;
    }
}