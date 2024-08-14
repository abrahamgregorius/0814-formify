<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $timestamps = false;

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function answers() {
        return $this->hasMany(Answer::class);
    }

    public function getAnswersAttribute() {
        return $this->answers()->get()->flatMap(function($ans) {
            $arr = [];
            $arr[$ans->question->name] = $ans->value;
            return $arr;
        });
    }


}
