<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeRequest extends Model
{
    protected $fillable = ['user_id', 'reason', 'duration_minutes', 'granted_until'];

    protected function casts(): array
    {
        return [
            'granted_until' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function activeFor(User $user): ?self
    {
        return static::where('user_id', $user->id)
            ->where('granted_until', '>', now())
            ->latest('granted_until')
            ->first();
    }
}
