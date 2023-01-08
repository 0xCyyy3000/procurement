<?php

namespace App\Http\Controllers;

use App\Models\RequisitionNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequisitionNotificationController extends Controller
{
    public function index()
    {
        $user = User::where('id', Auth::id())->first();

        $notifs['contents'] = ($user->department <= 3) ?
            RequisitionNotification::join('requisitions', 'requisitions.req_id', '=', 'requisition_notifications.reference')
            ->join('departments', 'departments.id', '=', 'requisition_notifications.user_id')
            ->get(
                [
                    'requisitions.maker', 'requisitions.description',
                    'requisitions.status', 'requisitions.evaluator', 'requisitions.message',
                    'departments.department', 'requisition_notifications.*'
                ]
            ) : RequisitionNotification::join('requisitions', 'requisitions.req_id', '=', 'requisition_notifications.reference')
            ->join('departments', 'departments.id', '=', 'requisition_notifications.user_id')
            ->join('users', 'users.id', '=', 'requisitions.user_id')
            ->where('users.id', $user->id)
            ->get(
                [
                    'requisitions.maker', 'requisitions.description',
                    'requisitions.status', 'requisitions.evaluator', 'requisitions.message',
                    'departments.department', 'requisition_notifications.*'
                ]
            );

        $notifs['user'] = $user->id;

        return response()->json($notifs);
    }
}
