<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PklStudent extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'nis',
        'jk',
        'tgl_lahir',
        'kelas',
        'token_id',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function (self $student) {
            if (empty($student->{$student->getKeyName()})) {
                $student->{$student->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * The token assigned to the PKL student.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function token()
    {
        return $this->belongsTo(Token::class);
    }
}
