<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Vcard extends Model
{
    use HasFactory;
    use SoftDeletes;
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
        'custom_data'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'phone_number');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'vcard', 'phone_number');
    }
    public function categories()
    {
        return $this->belongsToMany(DefaultCategory::class, 'categories', 'vcard', 'name');
    }
}

