<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Tickets\Controllers\TicketController;
use Modules\Documents\Controllers\DocumentController;
use Modules\Notifications\Controllers\NotificationController;

Route::middleware(['auth:sanctum'])->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles.permissions');
    });

    // Tickets API
    Route::apiResource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/assign', [TicketController::class, 'assign']);
    Route::post('tickets/{ticket}/status', [TicketController::class, 'changeStatus']);

    // Documents API
    Route::apiResource('documents', DocumentController::class);
    Route::get('documents/{document}/download', [DocumentController::class, 'download']);
    Route::post('documents/{document}/version', [DocumentController::class, 'uploadVersion']);
    Route::post('documents/{document}/share', [DocumentController::class, 'share']);

    // Notifications API
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
});
