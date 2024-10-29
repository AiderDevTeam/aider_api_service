<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'product_id',
        'name'
    ];

    const BLACK_TICKET = 'black_ticket';

    const BLACK_TICKET_TAGS = [
        'blackticket',
        'black-ticket',
        'black_ticket',
        'ticket'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
