<?php

namespace App\Helpers;

class RequestHelpers
{
    /**
     * prepareUpsertRequest
     * param1 ->
     */
    public static function prepareUpsertRequest($arr_fixed_values, $arr_different_values)
    {
        // Log::info('inside helper');
        // Log::info($arr_fixed_values);
        // Log::info($arr_different_values);
        $total_records = count($arr_different_values[array_key_first($arr_different_values)]);
        $final_request = [];

        for ($i = 0; $i < $total_records; $i++) {
            $record_to_insert = [];
            foreach ($arr_different_values as $key => $value) {
                // Log::info($key);
                if ($key != 'id') {
                    $record_to_insert += [$key => $value[$i]];
                } else if ($key == 'id' && $value[$i] > 0) {
                    $record_to_insert += [$key => $value[$i]];
                }
            }
            foreach ($arr_fixed_values as $fixedKey => $fixedValue) {
                $record_to_insert += [$fixedKey => $fixedValue];
            }
            // Log::info($record_to_insert);
            array_push($final_request, $record_to_insert);
        }
        return $final_request;
    }

    /**
     * get parameters for pagination from request
     */
    public static function getPaginationParams($request)
    {
        $orderBy = 'app_version';
        $orderDirection = 'desc';
        $per_page = '10';
        $search = null;
        if ($request->has('orderDirection')) $orderDirection = $request->query('orderDirection');
        if ($request->has('orderBy')) $orderBy = $request->query('orderBy');
        if ($request->has('per_page')) $per_page = $request->query('per_page');
        if ($request->has('search')) $search = $request->query('search');

        return ['orderBy' => $orderBy, 'orderDirection' => $orderDirection, 'per_page' => $per_page, 'search' => $search];
    }
}
