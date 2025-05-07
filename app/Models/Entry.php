<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vault_id',
        'title',
        'username',
        'password',
        'url',
        'notes',
        'icon',
        'category',
        'favorite',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_used' => 'datetime',
        'favorite' => 'boolean',
    ];

    /**
     * Get the vault that owns the entry.
     */
    public function vault()
    {
        return $this->belongsTo(Vault::class);
    }

    /**
     * Get the custom fields for the entry.
     */
    public function customFields()
    {
        return $this->hasMany(CustomField::class);
    }

    /**
     * Get the tags for the entry.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    
}