<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vcard extends Model
{
    use HasFactory;

    protected $table = 'vcards';
    protected $primaryKey = 'phone_number';
    public $incrementing = false; // If the primary key is not auto-incrementing

    protected $fillable = [
        'name',
        'email',
        'photo_url',
        'balance',
        'max_debit',
        'password',
        'confirmation_code',
    ];
}

