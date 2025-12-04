<?php

namespace Modules\Tickets\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'ticket_status_history';

    protected $fillable = [
        'ticket_id',
        'changed_by',
        'old_status',
        'new_status',
        'comment',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
