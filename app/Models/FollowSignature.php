<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowSignature extends Model
{
    use HasFactory;
    protected $table = 'follow_signatures';
    protected $guarded = [];
}
