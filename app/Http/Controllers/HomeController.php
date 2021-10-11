<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public $org_depth = 0;

    public function index(){
        return $this->get_employees_by_hierarchy(0,0,[]);
    }

    public function get_employees_by_hierarchy($_employee_id = 0, $_depth = 0, $_org_array = [])
    {
        if ($this->org_depth < $_depth) {
            $this->org_depth = $_depth;
        }

        $_depth++;


        $_query = "";
        if (!$_employee_id) {
            $_query .= "user_id IS NULL OR user_id = 0";
        } else {
            $_query .= "user_id = " . $_employee_id;
        }

        $result = DB::table('todos')->whereRaw($_query)->get();
        foreach($result as $key => $item){
            $result[$key]->depth = $_depth;
            array_push($_org_array, $item);
            $_org_array = $this->get_employees_by_hierarchy(
                $item->user_id,
                $_depth,
                $_org_array
            );
        }

        /*$_result = $this->query($_query);
        while ($_row = $_result->fetchRow()) {
            $_row['depth'] = $_depth;
            array_push($_org_array, $_row);
            $_org_array = $this->get_employees_by_hierarchy(
                $_row['employee_manager_id'],
                $_depth,
                $_org_array
            );
        }*/

        return $_org_array;
    }
}
