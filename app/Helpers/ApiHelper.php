<?php


namespace App\Helpers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Client;

class ApiHelper
{
    /**
     * @param $status boolean
     * @Desc true|false
     * @param $code int
     * @Desc status Code
     * @param $message string
     * @Desc  Response Message
     * @param $data array|string
     * @Desc Either data or message
     * @return Collection
     */
    static function GenerateApiResponse($status, $code, $message=null, $data=null)
    {
        return response([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code)->header('Content-Type', 'application/json');
    }

    static function filterData($data)
    {
        $data->each(function ($item){
            $item->date_time = date('M d Y H:i a', strtotime($item->date_time));
            $item->category_name = $item->category->name ?? '';
            $item->status = self::taskStatus($item->status);
            unset($item->category);
            unset($item->category_id);
        });
        return $data;
    }

    static function taskStatus($val)
    {
        $status = 'Pending';
        switch ($val) {
            case 1:
                $status = "Snoozed";
                break;
            case 2:
                $status = "Completed";
                break;
            case 3:
                $status = "Overdue";
                break;
        }
        return $status;
    }

}