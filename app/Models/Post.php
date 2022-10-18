<?php

namespace App\Models;

use App\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, ModelTrait;
    
    protected $fillable = ['content', 'user_id', 'topic_id'];
}
