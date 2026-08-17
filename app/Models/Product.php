<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['id_group', 'name'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    public $timestamps = false;

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'id_group');
    }

    public function price(): HasOne
    {
        return $this->hasOne(Price::class, 'id_product');
    }

    public function formattedPrice(): string
    {
        $value = $this->listed_price ?? $this->price?->price;

        if ($value === null) {
            return 'цена не указана';
        }

        return number_format((float) $value, 0, '.', ' ').' руб.';
    }
}
