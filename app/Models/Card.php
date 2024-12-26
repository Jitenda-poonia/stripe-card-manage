<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'stripe_card_id',
        'last4',
        'brand',
        'is_default',
    ];

    /**
     * Define the relationship between Card and User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only the default card.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
