<?php


namespace App\Libraries;


use App\Models\Request;

/**
 * Class RequestLibrary
 * @package App\Libraries
 */
abstract class RequestLibrary
{


    /**
     * @param int $request_id
     * @param mixed ...$params
     * @return Request|null
     * @throws \Exception
     */
    public static function get(int $request_id, array ...$params): ?Request
    {

        $params = !empty($params) ? $params[0] : $params;

        $in = []; // values for whereIn() eloquent method
        $not_in = []; // values for whereNotIn() eloquent method

        // populating params for query
        foreach ($params as $param) {
            $param > 0 ? $in[] = $param : $not_in[] = abs($param);
        }

        if (!empty($params))
            $request = Request::where('id', $request_id)
                ->whereIn('status_id', $in)
                ->whereNotIn('status_id', $not_in)
                ->get();
        else
            $request = Request::where('id', $request_id)->get();


        if ($request->isEmpty())
            throw new \Exception("Zahtev nije pronađen.");


        return $request->first();
    }

}
