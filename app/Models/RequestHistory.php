<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_request_id', 'user_id',
        'from_status', 'to_status', 'is_internal', 'comment',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => RequestStatus::class,
            'to_status'   => RequestStatus::class,
            'is_internal' => 'boolean',
        ];
    }

    public function clientRequest(): BelongsTo { return $this->belongsTo(ClientRequest::class); }
    public function user(): BelongsTo          { return $this->belongsTo(User::class); }
}
