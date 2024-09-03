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
    public function index(Request $request, User $user, Notification $notificaiton) {
        $notifications = new Collection();
        
        if ($user->department == '医学工程科'){
            if ($user->can('can_engineer_approve_equipment')) {
                $notifications = Notification::where('permission', '医学工程科')->get();
            }
        } else {
            $notifications = Notification::where('permission', $user->department)->get();
        }
        $notifications_user = Notification::where("user_id", $user->id)->get();
        $notifications = $notifications->merge($notifications_user);
        if ($user->can('can_audit_payment_record')) {
            $notifications_audit = Notification::where('permission', 'can_audit_payment_record')->get();
            $notifications = $notifications->merge($notifications_audit);
        }
        if ($user->can('can_process_payment_record')) {
            $notifications_process = Notification::where('permission', 'can_process_payment_record')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_apply_equipment')) {
            $notifications_process = Notification::where('permission', 'can_apply_equipment')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_survey_equipment')) {
            $notifications_process = Notification::where('permission', 'can_survey_equipment')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_approve_equipment')) {
            $notifications_process = Notification::where('permission', 'can_approve_equipment')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_tender_equipment')) {
            $notifications_process = Notification::where('permission', 'can_tender_equipment')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_install_equipment' && $user->department == '医学工程科')) {
            $notifications_process = Notification::where('permission', 'can_install_equipment')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_contract_equipment')) {
            $notifications_process = Notification::where('permission', 'can_contract_equipment')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_engineer_approve_equipment')) {
            $notifications_process = Notification::where('permission', 'can_engineer_approve_equipment')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_warehouse_equipment')) {
            $notifications_process = Notification::where('permission', 'can_warehouse_equipment')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_document_payment_process_record')) {
            $notifications_process = Notification::where('permission', 'can_document_payment_process_record')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_finance_audit_payment_process_record')) {
            $notifications_process = Notification::where('permission', 'can_finance_audit_payment_process_record')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_dean_audit_payment_process_record')) {
            $notifications_process = Notification::where('permission', 'can_dean_audit_payment_process_record')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_process_payment_process_record')) {
            $notifications_process = Notification::where('permission', 'can_process_payment_process_record')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_survey_instrument')) {
            $notifications_process = Notification::where('permission', 'can_survey_instrument')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_contract_instrument')) {
            $notifications_process = Notification::where('permission', 'can_contract_instrument')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_install_instrument')) {
            $notifications_process = Notification::where('permission', 'can_install_instrument')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_engineer_approve_instrument')) {
            $notifications_process = Notification::where('permission', 'can_engineer_approve_instrument')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_engineer_approve_repair')) {
            $notifications_process = Notification::where('permission', 'can_engineer_approve_repair')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_approve_contracts')) {
            $notifications_process = Notification::where('permission', 'can_approve_contracts')->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_create_payment_process')) {
            $notifications_process = Notification::where('permission', 'can_create_payment_process')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_apply_tempory_consumable_record')) {
            $notifications_process = Notification::where('permission', 'can_apply_tempory_consumable_record')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_purchase_tempory_consumable_record')) {
            $notifications_process = Notification::where('permission', 'can_purchase_tempory_consumable_record')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_approve_tempory_consumable_record')) {
            $notifications_process = Notification::where('permission', 'can_approve_tempory_consumable_record')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_stop_tempory_consumable_record')) {
            $notifications_process = Notification::where('permission', 'can_stop_tempory_consumable_record')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_apply_consumable_record')) {
            $notifications_process = Notification::where('permission', 'can_apply_consumable_record')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_purchase_consumable_record')) {
            $notifications_process = Notification::where('permission', 'can_purchase_consumable_record')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_approve_consumable_record')) {
            $notifications_process = Notification::where('permission', 'can_approve_consumable_record')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_engineer_approve_consumable_record')) {
            $notifications_process = Notification::where('permission', 'can_engineer_approve_consumable_record')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_back_consumable_record')) {
            $notifications_process = Notification::where('permission', 'can_back_consumable_record')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_delete_consumable_record')) {
            $notifications_process = Notification::where('permission', 'can_delete_consumable_record')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_purchase_consumable_list')) {
            $notifications_process = Notification::where('permission', 'can_purchase_consumable_list')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_approve_consumable_list')) {
            $notifications_process = Notification::where('permission', 'can_approve_consumable_list')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_engineer_approve_consumable_list')) {
            $notifications_process = Notification::where('permission', 'can_engineer_approve_consumable_list')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        if ($user->can('can_delete_consumable_list')) {
            $notifications_process = Notification::where('permission', 'can_delete_consumable_list')->where('department_id', $user->department_id)->get();
            $notifications = $notifications->merge($notifications_process);
        }
        return  NotificationResource::collection($notifications);
    }

    public function delete(Request $request, Notification $notificaiton) {
        $notificaiton = Notification::find($request->id);
        $notificaiton->delete();
        return response()->json([])->setStatusCode(200);
    }
}
