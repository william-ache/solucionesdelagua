<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_id',
        'amount_paid',
        'payment_date',
        'payment_method',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }

    /**
     * Automatic business logic trigger.
     * When a credit payment is registered, subtract the amount paid from the parent credit in-line.
     */
    protected static function booted()
    {
        static::created(function ($payment) {
            $credit = $payment->credit;
            if ($credit) {
                // Deduct balance and ensure it does not drop below 0
                $credit->balance_due = max(0, $credit->balance_due - $payment->amount_paid);
                
                // If fully paid, flag status as paid
                if ($credit->balance_due <= 0) {
                    $credit->status = 'paid';
                    
                    // Also flag the original sale status as 'paid' since credit was resolved
                    if ($credit->sale) {
                        $credit->sale->status = 'paid';
                        $credit->sale->save();
                    }
                }
                
                $credit->save();
            }
        });
    }
}
