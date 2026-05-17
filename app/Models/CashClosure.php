<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashClosure extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'month', 'year',
        'rate_bcv', 'rate_usdt',
        'initial_amount_usd', 'initial_amount_ves',
        'total_sales_usd', 'total_sales_bs',
        'audited_usd', 'audited_ves',
        'difference_usd', 'difference_ves',
        'status', 'closed_at',
        'opened_by_user_id', 'closed_by_user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'closed_at' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(CashClosureDetail::class);
    }
}
