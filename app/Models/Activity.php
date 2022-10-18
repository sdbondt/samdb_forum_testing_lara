<?php

namespace App\Models;

use App\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    use ModelTrait;
    
    protected $fillable = ['user_id', 'action'];

    const TOPIC_CREATED = 'TOPIC_CREATED';
    const POST_CREATED = 'POST_CREATED';
    const POST_LIKED = 'POST_LIKED';
}
