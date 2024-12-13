<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock_item extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class, 'code_item', 'code');
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'code_item', 'code');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
