<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\returnSelf;

function dataAttribute($data, $attribute1, $attribute2)
{
    foreach ($data as $key => $value) {
        $str = explode('_', $key);
        $key = ucwords(join(" ", $str));
        $arrayData[] = [
            $attribute1 => $key,
            $attribute2 => $value
        ];
        $array =  json_encode($arrayData);
    }
    return $arrayData;
}
