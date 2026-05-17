<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'type',
        'amount',
        'description',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
