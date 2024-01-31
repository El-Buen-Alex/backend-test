<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable=['name', 'slug', 'status_id', 'user_id', 'description'];


    protected $dates=['created_at'];


    protected $table='tasks';

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function status(){
        return $this->hasOne(TaskStatus::class, 'id', 'status_id');
    }
}
