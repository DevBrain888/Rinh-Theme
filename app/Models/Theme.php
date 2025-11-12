<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Theme extends Model
{
    protected $fillable = [
        'title',
        'description',
        'group',
        'status',
        'assigned_to',
        'supervisor_id',
        'reserved_by_group',
        'reserved_at',
    ];

    protected $casts = [
        'status' => 'string',
        'reserved_at' => 'datetime',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'theme_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class);
    }
}
