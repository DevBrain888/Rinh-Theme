<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supervisor extends Model
{
    protected $fillable = [
        'name',
        'position',
        'email',
        'description',
    ];

    /**
     * Получить все темы, назначенные этому руководителю
     */
    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class);
    }
}
