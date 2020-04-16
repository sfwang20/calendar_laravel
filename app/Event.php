<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'year', 'month', 'date', 'start_time', 'end_time', 'description'];
    public function user()
    {
      return $this->belongsTo('App\User');
    }
}
