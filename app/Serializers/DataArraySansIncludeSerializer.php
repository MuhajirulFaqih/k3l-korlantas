<?php
/**
 * Created by PhpStorm.
 * User: athasamid
 * Date: 1/4/18
 * Time: 10:33 PM
 */

namespace App\Serializers;


use League\Fractal\Serializer\DataArraySerializer;

class DataArraySansIncludeSerializer extends DataArraySerializer {
    public function mergeIncludes($transformedData, $includedData)
    {
        $includedData = array_map(function ($include) {
            return $include['data'];
        }, $includedData);

        /*$transformedData = array_map(function ($transformed) {
            return $transformed['data'];
        }, $transformedData);*/

        return parent::mergeIncludes($transformedData, $includedData);
    }
}