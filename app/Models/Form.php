<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $guarded = []; 
    public $timestamps = false;

    public function getRouteKeyName() {
        return "slug";
    }

    public function questions() {
        return $this->hasMany(Question::class);
    }

    public function allowedDomains() {
        return $this->hasMany(AllowedDomain::class);    
    }
}
