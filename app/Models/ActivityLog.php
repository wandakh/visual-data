<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'action', 'subject_id', 'description', 'created_at'];

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

    /**
     * Helper singkat buat nyatet aktivitas dari controller manapun, tanpa
     * perlu nulis ulang Query Builder-nya tiap kali.
     */
    public static function record(string $action, ?int $subjectId, ?int $userId, ?string $description = null): void
    {
        static::create([
            'user_id' => $userId,
            'action' => $action,
            'subject_id' => $subjectId,
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}
