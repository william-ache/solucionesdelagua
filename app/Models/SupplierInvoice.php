<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'amount',
        'status',
        'description',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
