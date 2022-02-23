<?php

namespace App\Models;

use App\Contracts\AdvancedSearchable;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Order extends Model implements AdvancedSearchable
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'status' => OrderStatus::class,
        'ordered_at' => 'datetime'
    ];

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getTotalValueAttribute()
    {
        return $this->orderProducts()->sum('unit_price');
    }

    public static function scopeAdvancedSearch($query, $param)
    {
        $query->join('clients', 'clients.id', 'orders.client_id')
            ->select('clients.name', 'orders.*')
            ->where('clients.name', 'like', "%$param%");
    }
}
