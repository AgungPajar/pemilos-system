<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paslon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'name',
        'leader_name',
        'deputy_name',
        'image_path',
        'tagline',
        'vision',
        'mission',
        'program',
    ];

    /**
     * A paslon can have many tokens that voted for them.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    /**
     * Get combined display name.
     *
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        $leader = $this->leader_name ?: $this->name;
        $deputy = $this->deputy_name;

        if ($leader && $deputy) {
            return "{$leader} & {$deputy}";
        }

        return $leader ?? '-';
    }

    /**
     * Get initials used for placeholder thumbnails.
     *
     * @return string
     */
    public function getInitialsAttribute(): string
    {
        $names = array_filter([$this->leader_name, $this->deputy_name]);

        if (empty($names)) {
            $fallback = $this->display_name ?: $this->name ?: 'P';
            return strtoupper(substr($fallback, 0, 1));
        }

        $initials = collect($names)->map(function ($segment) {
            return strtoupper(substr(trim($segment), 0, 1));
        })->implode('');

        return substr($initials, 0, 2);
    }
}
