<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ClientRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'service_id', 'client_id', 'manager_id',
        'status', 'subject', 'body',
        'contact_name', 'contact_email', 'contact_phone',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return ['status' => RequestStatus::class];
    }

    protected static function booted(): void
    {
        static::creating(function (ClientRequest $request) {
            if (empty($request->number)) {
                $request->number = self::generateNumber();
            }
        });
    }

    public static function generateNumber(): string
    {
        return 'REQ-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }

    public function service(): BelongsTo   { return $this->belongsTo(Service::class); }
    public function client(): BelongsTo    { return $this->belongsTo(User::class, 'client_id'); }
    public function manager(): BelongsTo   { return $this->belongsTo(User::class, 'manager_id'); }

    public function histories(): HasMany
    {
        return $this->hasMany(RequestHistory::class)->orderBy('created_at');
    }

    public function publicHistories(): HasMany
    {
        return $this->hasMany(RequestHistory::class)
            ->where('is_internal', false)
            ->orderBy('created_at');
    }
}
