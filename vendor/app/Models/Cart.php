<?php

namespace App\Models;

use App\Custom\Status;
use App\Http\Resources\ProductResource;
use App\Http\Resources\VendorResource;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Cart extends Model
{
    use HasFactory, RealtimeModel, SoftDeletes;

    protected $fillable = [
        'external_id',
        'vendor_id',
        'user_id',
        'product_id',
        'quantity',
        'is_checked_out',
        'order_id',
        'unit_price',
        'discounted_amount',
        'discounted_method',
        'deleted_at',
        'unique_id',
        'size',
        'color'
    ];

    protected $dates = ['deleted_at'];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function review(): MorphOne
    {
        return $this->morphOne(Review::class, 'reviewable');
    }

    public function isReviewable(): bool
    {
        return $this->order?->status === Status::SUCCESS;
    }

    public function isReviewed(): bool
    {
        return $this->review()->exists();
    }

    public function increase(): bool|int
    {
        if (($this->quantity + 1) <= $this->product->quantity)
            return $this->increment('quantity');

        return false;
    }

    public function decrease(): bool|int
    {
        if (($this->quantity) > 1)
            return $this->decrement('quantity');

        return false;
    }

    public function toRealtimeData(): array
    {
        return [
            'userExternalId' => $this->user->external_id,
            'externalId' => $this->external_id,
            'vendorId' => $this->vendor_id,
            'product' => new ProductResource($this->product),
            'quantity' => $this->quantity,
            'orderId' => $this->order_id,
            'unitPrice' => $this->unit_price,
            'discountedAmount' => $this->discounted_amount,
            'discountedMethod' => $this->discounted_method,
            'deletedAt' => $this->deleted_at,
            'isCheckedOut' => (boolean)$this->is_checked_out,
            'size' => $this->size,
            'color' => $this->color,
            'ts' => Carbon::parse($this->created_at)->timestamp,
            'vendor' => new VendorResource($this->vendor)
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'external_id');
    }

}
