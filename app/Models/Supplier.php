<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'document_id',
        'phone',
        'email',
        'balance',
    ];

    public function invoices()
    {
        return $this->hasMany(SupplierInvoice::class);
    }
}
