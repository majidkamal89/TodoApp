<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $fillable = [
        'name','user_id'
    ];

    public function scopeUserCategory($query, $userid, $id)
    {
        return $query->where('user_id', $userid)->where('id', $id);
    }
}
