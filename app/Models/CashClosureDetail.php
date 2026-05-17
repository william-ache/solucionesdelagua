<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashClosureDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_closure_id',
        'payment_method',
        'type',
        'amount_usd',
        'amount_ves',
        'reference_number',
        'description'
    ];

    public function closure()
    {
        return $this->belongsTo(CashClosure::class, 'cash_closure_id');
    }
}
