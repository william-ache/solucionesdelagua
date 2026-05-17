<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'account_type',
        'account_number',
        'initial_balance',
        'current_balance',
    ];

    public function movements()
    {
        return $this->hasMany(BankMovement::class);
    }
}
