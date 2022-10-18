<?php

namespace App\Models;

use App\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory, ModelTrait;

    protected $fillable = ['subject'];

}
