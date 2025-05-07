<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vault extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'icon',
        'encryption_key',
        'is_default',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'encryption_key',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the vault.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the entries for the vault.
     */
    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    /**
     * Get the shared access records for this vault.
     */
    public function sharedVaults()
    {
        return $this->hasMany(SharedVault::class);
    }

    
}