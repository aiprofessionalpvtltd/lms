<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivityView extends Model
{
    protected $table = 'log_activities_view';
    public $timestamps = false; // Views don't have `timestamps`
}
