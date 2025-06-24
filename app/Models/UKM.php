<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UKM extends Model
{
    // Explicit table name to avoid Laravel pluralisation (u_k_m_s)
    protected $table = 'ukms';
    protected $fillable = [
        'nama',
        'kode'
    ];

    // Users relationship (one-to-many)
    public function users(): HasMany
    {
        // explicitly set foreign key to match column 'ukm_id' on users table
        return $this->hasMany(User::class, 'ukm_id');
    }
}
