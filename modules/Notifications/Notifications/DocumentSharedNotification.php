<?php

namespace Modules\Notifications\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Documents\Models\Document;

class DocumentSharedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Document $document,
        public string $permission
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Document Shared with You: ' . $this->document->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A document has been shared with you.')
            ->line('Title: ' . $this->document->title)
            ->line('Permission: ' . ucfirst($this->permission))
            ->action('View Document', route('documents.show', $this->document))
            ->line('Thank you for using SmartDesk!');
    }

    public function toArray($notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'document_title' => $this->document->title,
            'permission' => $this->permission,
            'message' => 'A document has been shared with you: ' . $this->document->title,
        ];
    }
}
