<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Http\Resources\NotificationResource;

class NotificationsController extends Controller
{
    public function index(Request $request, Notification $notificaiton) {
        $query = $notificaiton->query();
        $notificaitons = $query->paginate();

        return NotificationResource::collection($notificaitons);
    }

    public function delete(Request $request, Notification $notificaiton) {
        $notificaiton->delete();
        return response()->json([])->setStatusCode(201);
    }
}
