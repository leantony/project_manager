<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserProjects extends \Eloquent
{
    public $fillable = ['project_id', 'email', 'username'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Project(){

        return $this->belongsTo(\App\Models\Projects::class);
    }
}
