<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'created_by'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
