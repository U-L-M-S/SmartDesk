<?php

use Illuminate\Support\Facades\Route;
use Modules\Tickets\Controllers\TicketController;
use Modules\Tickets\Controllers\TicketCommentController;
use Modules\Documents\Controllers\DocumentController;
use Modules\Notifications\Controllers\NotificationController;
use Modules\Notifications\Controllers\SystemNotificationController;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard')->middleware(['auth']);

// Authentication routes (Laravel Breeze or similar would typically handle these)
Route::middleware(['auth'])->group(function () {

    // Tickets
    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/assign', [TicketController::class, 'assign'])
        ->name('tickets.assign')
        ->middleware('permission:tickets.assign');
    Route::post('tickets/{ticket}/status', [TicketController::class, 'changeStatus'])
        ->name('tickets.change-status')
        ->middleware('permission:tickets.edit');

    // Ticket Comments
    Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])
        ->name('tickets.comments.store');
    Route::delete('tickets/{ticket}/comments/{comment}', [TicketCommentController::class, 'destroy'])
        ->name('tickets.comments.destroy');

    // Documents
    Route::resource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');
    Route::post('documents/{document}/version', [DocumentController::class, 'uploadVersion'])
        ->name('documents.version')
        ->middleware('permission:documents.versions');
    Route::post('documents/{document}/share', [DocumentController::class, 'share'])
        ->name('documents.share')
        ->middleware('permission:documents.share');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');

    // System Notifications (Admin only)
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::resource('system-notifications', SystemNotificationController::class);
        Route::get('api/system-notifications/active', [SystemNotificationController::class, 'active'])
            ->name('system-notifications.active');
    });
});
