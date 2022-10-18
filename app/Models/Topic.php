<?php

namespace App\Models;

use App\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory, ModelTrait;

    protected $fillable = ['title', 'body', 'user_id', 'section_id'];
}
