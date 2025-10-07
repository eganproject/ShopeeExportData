<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserActivityController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $actions = UserActivity::query()->select('action')->distinct()->orderBy('action')->pluck('action');
        return view('user_activities.index', compact('users', 'actions'));
    }

    public function data(Request $request)
    {
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $searchValue = $request->input('search.value');

        $baseQuery = UserActivity::query();
        $recordsTotal = (clone $baseQuery)->count();

        $query = UserActivity::query()
            ->leftJoin('users', 'user_activities.user_id', '=', 'users.id')
            ->select(
                'user_activities.*',
                DB::raw("COALESCE(users.name, CONCAT('User #', user_activities.user_id)) as user_name"),
                'users.email as user_email'
            );

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_activities.user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('user_activities.action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('user_activities.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('user_activities.created_at', '<=', $request->date_to);
        }

        // Global search
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('user_activities.description', 'like', "%{$searchValue}%")
                  ->orWhere('user_activities.action', 'like', "%{$searchValue}%")
                  ->orWhere('user_activities.route', 'like', "%{$searchValue}%")
                  ->orWhere('user_activities.url', 'like', "%{$searchValue}%")
                  ->orWhere('user_activities.ip', 'like', "%{$searchValue}%")
                  ->orWhere('users.name', 'like', "%{$searchValue}%");
            });
        }

        // Ordering
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = [
            0 => 'user_activities.created_at',
            1 => 'users.name',
            2 => 'user_activities.description',
            3 => 'user_activities.action',
            4 => 'user_activities.route',
            5 => 'user_activities.method',
            6 => 'user_activities.ip',
        ];
        $orderBy = $columns[$orderColumnIndex] ?? 'user_activities.created_at';
        $orderDir = in_array(strtolower($orderDir), ['asc', 'desc']) ? $orderDir : 'desc';
        $query->orderBy($orderBy, $orderDir)->orderBy('user_activities.id', 'desc');

        $recordsFiltered = (clone $query)->count();

        $rows = $query->skip($start)->take($length)->get();

        $data = $rows->map(function ($row) {
            return [
                'created_at' => optional($row->created_at)->format('Y-m-d H:i:s'),
                'user' => trim(($row->user_name ?? '-') . (isset($row->user_email) ? " ({$row->user_email})" : '')),
                'description' => $row->description ?? '-',
                'action' => $row->action,
                'route' => $row->route,
                'method' => $row->method,
                'ip' => $row->ip,
            ];
        })->toArray();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}

