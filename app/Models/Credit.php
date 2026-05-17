<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'total_debt',
        'balance_due',
        'due_date',
        'status',
    ];

    protected $casts = [
        'total_debt' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments()
    {
        return $this->hasMany(CreditPayment::class);
    }
}
