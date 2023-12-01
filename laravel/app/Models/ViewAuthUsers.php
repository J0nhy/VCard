<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewAuthUsers extends Model
{
    use HasFactory;

    protected $table = 'view_auth_users';

    protected $fillable = [
        'user_type',
        'username',
        'email',
        'password',
        'name',
        'blocked',
        'photo_url'
    ];
}