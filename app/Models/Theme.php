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
        'status',
        'assigned_to',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'theme_id');
    }
}
