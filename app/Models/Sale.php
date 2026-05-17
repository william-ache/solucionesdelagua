<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'total_amount',
        'currency',
        'status',
        'date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function credit()
    {
        return $this->hasOne(Credit::class);
    }

    /**
     * Automatic lifecycle hook.
     * When a sale is created with status 'credit', automatically create the Credit record.
     */
    protected static function booted()
    {
        static::created(function ($sale) {
            if ($sale->status === 'credit') {
                $sale->credit()->create([
                    'total_debt' => $sale->total_amount,
                    'balance_due' => $sale->total_amount,
                    'due_date' => $sale->date ? $sale->date->addDays(30) : now()->addDays(30),
                    'status' => 'pending',
                ]);
            }
        });
    }
}
