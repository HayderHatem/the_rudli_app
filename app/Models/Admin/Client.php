<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $connection = 'admin';
    protected $table = 'clients';

    protected $fillable = [
        'name',
        'identifier',
        'schema_name',
        'domain',
        'subscription_id',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get the subscription for this client
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the admin users for this client
     */
    public function adminUsers()
    {
        return $this->hasMany(AdminUser::class);
    }
}
