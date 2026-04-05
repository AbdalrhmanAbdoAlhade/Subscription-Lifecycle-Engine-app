<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanPrice extends Model
{
    protected $fillable = [
        'plan_id',
        'billing_cycle',
        'currency',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}