<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_name',
        'amount',
        'currency',
        'payment_date',
        'reference_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Retrieve the exchange rate active on or closest prior to the payment date.
     */
    public function getExchangeRate()
    {
        return ExchangeRate::where('date', '<=', $this->payment_date)
            ->orderBy('date', 'desc')
            ->first();
    }

    /**
     * Accessor: Dynamically returns the calculated amount in the alternative currency.
     * USD -> VES or VES -> USD based on the exchange rate of the payment date.
     */
    public function getConvertedAmountAttribute()
    {
        $rateRecord = $this->getExchangeRate();
        if (!$rateRecord || $rateRecord->rate <= 0) {
            return null;
        }

        if ($this->currency === 'USD') {
            return round($this->amount * $rateRecord->rate, 2);
        } else {
            return round($this->amount / $rateRecord->rate, 2);
        }
    }

    /**
     * Accessor: Returns the ISO code of the converted currency.
     */
    public function getConvertedCurrencyAttribute()
    {
        return $this->currency === 'USD' ? 'VES' : 'USD';
    }
}
