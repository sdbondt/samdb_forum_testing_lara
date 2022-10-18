<?php

namespace App\Models;

use App\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory, ModelTrait;

    protected $fillable = ['user_id', 'post_id', 'type'];
}
