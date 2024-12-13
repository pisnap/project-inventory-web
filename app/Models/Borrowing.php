<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowing extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function stock_item(): BelongsTo
    {
        return $this->belongsTo(Stock_item::class, 'code_item', 'code');
    }
}
