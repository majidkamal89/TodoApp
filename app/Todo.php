<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{

    protected $fillable = [
        'name','description','date_time','user_id','category_id','status'
    ];

    // Status Types
    const T_PENDING = 0;
    const T_SNOOZED = 1;
    const T_COMPLETED = 2;
    const T_OVERDUE = 3;
    const PER_PAGE = 10;

    public function scopeUserTask($query, $userid, $id)
    {
        return $query->where('user_id', $userid)->where('id', $id);
    }

    public function scopeTaskByDate($query, $userid, $date)
    {
        return $query->where('user_id', $userid)->whereDay('date_time', '=', $date);
    }

    public function scopeTaskByMonth($query, $userid, $month)
    {
        return $query->where('user_id', $userid)->whereMonth('date_time', '=', $month);
    }

    public function category(){
        return $this->hasOne(Category::class, 'id', 'category_id')->select(['name','id']);
    }
}
