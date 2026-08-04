<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutsourcedActivity extends Model
{
    use HasFactory;
    protected $table = "outsourced_activities";
    protected $guarded = [];
}
