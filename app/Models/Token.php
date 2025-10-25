<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    public const SESSION_KEY = 'token_auth_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'paslon_id',
        'used_at',
        'used_ip',
        'used_user_agent',
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'used_at' => 'datetime',
    ];

    /**
     * Token belongs to a paslon when it has been used.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paslon()
    {
        return $this->belongsTo(Paslon::class);
    }

    /**
     * PKL student assigned to this token.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function pklStudent()
    {
        return $this->hasOne(PklStudent::class);
    }

    /**
     * Check if the token has already been used.
     *
     * @return bool
     */
    public function isUsed(): bool
    {
        return (bool) $this->used_at;
    }
}
