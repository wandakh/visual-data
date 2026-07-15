<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'action', 'description', 'created_at'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, ?int $userId, ?string $description = null): void
    {
        static::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}
