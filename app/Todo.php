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
    const PER_PAGE = 2;

    public function scopeUserTask($query, $userid, $id)
    {
        return $query->where('user_id', $userid)->where('id', $id);
    }

    public function category(){
        return $this->hasOne(Category::class, 'id', 'category_id')->select(['name','id']);
    }
}
