<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'name', 'is_active', 'token', 'confirmed_at', 'unsubscribed_at'];

    protected $casts = [
        'is_active'       => 'boolean',
        'confirmed_at'    => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (NewsletterSubscriber $sub) {
            $sub->token = Str::random(64);
        });
    }

    public function unsubscribe(): void
    {
        $this->update([
            'is_active'       => false,
            'unsubscribed_at' => now(),
        ]);
    }

    public function getUnsubscribeUrlAttribute(): string
    {
        return url("/api/newsletter/unsubscribe/{$this->token}");
    }
}
