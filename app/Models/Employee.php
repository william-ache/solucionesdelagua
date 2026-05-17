<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'identification_number',
        'base_salary_usd',
        'status',
    ];

    protected $casts = [
        'base_salary_usd' => 'decimal:2',
    ];

    public function payrollPayments()
    {
        return $this->hasMany(PayrollPayment::class);
    }
}
