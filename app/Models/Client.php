<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'document_id',
        'phone',
        'email',
        'address',
        'balance',
    ];

    public function transactions()
    {
        return $this->hasMany(ClientTransaction::class);
    }
}
