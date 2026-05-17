<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate',
        'date',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'date' => 'date',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
