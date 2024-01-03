<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use App\Http\Resources\NotificationResource;

class NotificationsController extends Controller
{
    public function index(Request $request, User $user, Notification $notificaiton) {
        $notifications = Notification::all();
        // $notifications = Notification::where('permission', $user->department)->get();
        // if ($user->can('can_audit_payment_record')) {
        //     $notifications_audit = Notification::where('permission', 'can_audit_payment_record')->get();
        //     $notifications = $notifications->merge($notifications_audit);
        // }
        // if ($user->can('can_process_payment_record')) {
        //     $notifications_process = Notification::where('permission', 'can_process_payment_record')->get();
        //     $notifications = $notifications->merge($notifications_process);
        // }
        return  NotificationResource::collection($notifications);
    }

    public function delete(Request $request, Notification $notificaiton) {
        $notificaiton->delete();
        return response()->json([])->setStatusCode(201);
    }
}
