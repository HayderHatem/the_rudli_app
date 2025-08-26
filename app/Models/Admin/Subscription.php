<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $connection = 'admin';
    protected $table = 'subscriptions';

    protected $fillable = [
        'name',
        'price',
        'billing_cycle',
        'features',
        'max_users',
        'max_storage',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'max_users' => 'integer',
        'max_storage' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the clients using this subscription
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
