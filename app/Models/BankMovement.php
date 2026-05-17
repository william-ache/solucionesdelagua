<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_account_id',
        'type',
        'category',
        'amount',
        'description',
        'reference_number',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
