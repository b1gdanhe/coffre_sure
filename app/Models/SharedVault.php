<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedVault extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vault_id',
        'user_id',
        'permission_level',
        'invitation_status',
        'encrypted_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'encrypted_key',
    ];

    /**
     * Get the vault that is being shared.
     */
    public function vault()
    {
        return $this->belongsTo(Vault::class);
    }

    /**
     * Get the user that the vault is shared with.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}