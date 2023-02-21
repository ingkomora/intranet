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
     * Getting request model
     * @throws \Exception
     */
    public static function get(int $request_id, int $status_id = null): ?Request
    {
        if ($status_id)
            $request = Request::where('id', $request_id)->where('status_id', $status_id)->get();
        else
            $request = Request::where('id', $request_id)->get();


        if ($request->isEmpty())
            throw new \Exception("Zahtev nije pronađen.");

        return $request->first();
    }

}
