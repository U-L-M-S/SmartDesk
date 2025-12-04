<?php

namespace Modules\Tickets\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'created_by',
        'assigned_to',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(TicketStatusHistory::class);
    }

    public function changeStatus(string $newStatus, User $user, ?string $comment = null): void
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;

        if ($newStatus === 'closed') {
            $this->closed_at = now();
        }

        $this->save();

        $this->statusHistory()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $user->id,
            'comment' => $comment,
        ]);
    }

    public function assignTo(User $user): void
    {
        $this->assigned_to = $user->id;
        $this->save();
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeAssignedTo($query, User $user)
    {
        return $query->where('assigned_to', $user->id);
    }
}
