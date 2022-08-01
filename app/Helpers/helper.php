<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

use function PHPUnit\Framework\returnSelf;

function dataAttribute($name, $data, $attribute1, $attribute2)
{
    foreach ($data as $key => $value) {
        $str = explode('_', $key);
        $key = ucwords(join(" ", $str));
        $arrayData[] = [
            $attribute1 => $key,
            $attribute2 => $value,
            "nilai_history" => 0
        ];
    }
    $res = [
        "name" => $name,
        "data" => $arrayData
    ];
    return $res;
}

function dataAttributeH($name, $data, $dataH, $attribute1, $attribute2, $attribute3)
{
    foreach ($dataH as $keyh => $valueH) {
        $arrayH[] = [$attribute3 => $valueH];
        // foreach ($dataH as $key => $valueH)
        // }
    }

    foreach ($data as $key => $value) {
        $str = explode('_', $key);
        $key = ucwords(join(" ", $str));
        $arrayData[] = [
            $attribute1 => $key,
            $attribute2 => $value
        ];
    }

    for ($x = 0; $x < count($arrayData); $x++) {
        $final[] = array_merge($arrayData[$x], $arrayH[$x]);
      }
    $res = [
        "name" => $name,
        "data" => $final
    ];
      
    return $res;
}
