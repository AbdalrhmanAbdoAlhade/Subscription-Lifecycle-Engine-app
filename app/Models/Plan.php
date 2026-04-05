<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'trial_days',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'trial_days' => 'integer',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(PlanPrice::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}