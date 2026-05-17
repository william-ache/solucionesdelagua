<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount_usd',
        'amount_ves',
        'exchange_rate_id',
        'description',
        'date',
    ];

    protected $casts = [
        'amount_usd' => 'decimal:2',
        'amount_ves' => 'decimal:2',
        'date' => 'date',
    ];

    public function exchangeRate()
    {
        return $this->belongsTo(ExchangeRate::class);
    }

    /**
     * Boot model hooks.
     * Auto-calculate missing amounts on system save based on the exchange rate.
     */
    protected static function booted()
    {
        static::saving(function ($transaction) {
            if ($transaction->exchange_rate_id) {
                // Fetch the exchange rate rate value
                $rate = $transaction->exchangeRate 
                    ? $transaction->exchangeRate->rate 
                    : ExchangeRate::find($transaction->exchange_rate_id)?->rate;
                
                if ($rate > 0) {
                    if (empty($transaction->amount_ves) && !empty($transaction->amount_usd)) {
                        $transaction->amount_ves = round($transaction->amount_usd * $rate, 2);
                    } elseif (empty($transaction->amount_usd) && !empty($transaction->amount_ves)) {
                        $transaction->amount_usd = round($transaction->amount_ves / $rate, 2);
                    }
                }
            }
        });
    }

    /**
     * Accessor to get the value in VES if only USD is available (fallback)
     */
    public function getConvertedToVesAttribute()
    {
        if ($this->amount_ves > 0) {
            return $this->amount_ves;
        }
        if ($this->exchangeRate && $this->amount_usd) {
            return round($this->amount_usd * $this->exchangeRate->rate, 2);
        }
        return 0.00;
    }

    /**
     * Accessor to get the value in USD if only VES is available (fallback)
     */
    public function getConvertedToUsdAttribute()
    {
        if ($this->amount_usd > 0) {
            return $this->amount_usd;
        }
        if ($this->exchangeRate && $this->amount_ves && $this->exchangeRate->rate > 0) {
            return round($this->amount_ves / $this->exchangeRate->rate, 2);
        }
        return 0.00;
    }
}
