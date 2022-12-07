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
        $user = User::where('id', Auth::id())->get('department');
        $notifs = '';

        $notifs = ($user[0]->department <= 3) ?
            $notifs = RequisitionNotification::join('requisitions', 'requisitions.req_id', '=', 'requisition_notifications.requisition_id')
            ->join('departments', 'departments.id', '=', 'requisition_notifications.user_id')
            ->get(
                [
                    'requisitions.maker', 'requisitions.description',
                    'requisitions.status', 'requisitions.evaluator', 'requisitions.message',
                    'departments.department', 'requisition_notifications.*'
                ]
            ) : "You're not an admin";

        return response()->json($notifs);
    }
}
