<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use App\Http\Resources\NotificationResource;

class NotificationsController extends Controller
{
    public function index(Request $request, User $user, Notification $notification) {
        $notifications = Notification::all();

        $filtered_notifications = $notifications->filter(function ($notification) use ($user) {
            if($notification->is_ignore === 'true') return false;
            // 如果通知user_id对应则推送
            if(!is_null($notification->user_id)) {
                if($notification->user_id === $user->id) return true;
                else return false;
            }
            // 如果通知有科室指定
            if($notification->department_id) {
                //如果科室指定相符
                if($notification->department_id === $user->department_id) {
                    // 如果有指定权限
                    if($notification->permission) {
                        //如果用户有权限
                        if($user->can($notification->permission)) {
                            return true;
                        } else {
                        //如果用户没有权限
                            return false;
                        }
                    } else {
                    // 如果没有指定权限
                        return true;
                    }
                //如果科室不相符
                } else {
                    return false;
                }
            } else {
            // 如果没有科室指定
                // 如果有指定权限
                if($notification->permission) {
                    //如果用户有权限
                    if($user->can($notification->permission)) {
                        return true;
                    } else {
                    //如果用户没有权限
                        return false;
                    }
                } else {
                // 如果没有指定权限
                    return true;
                }
            }
        });

        return  NotificationResource::collection($filtered_notifications);
    }

    public function ignore(Request $request, Notification $notification) {
        $notification->update([
            'is_ignore' => 'true',
        ]);
        return new NotificationResource($notification);
    }

    public function delete(Request $request, Notification $notification) {
        $notification = Notification::find($request->id);
        $notification->delete();
        return response()->json([])->setStatusCode(200);
    }
}
