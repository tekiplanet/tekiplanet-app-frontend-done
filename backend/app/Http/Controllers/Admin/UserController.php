<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with(['businessProfile', 'professional'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($request->account_type, function ($query, $type) {
                switch ($type) {
                    case 'business':
                        $query->whereHas('businessProfile');
                        break;
                    case 'professional':
                        $query->whereHas('professional');
                        break;
                    case 'student':
                        $query->whereDoesntHave('businessProfile')
                            ->whereDoesntHave('professional');
                        break;
                }
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            });

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'search' => $request->search,
                'account_type' => $request->account_type,
                'status' => $request->status,
            ],
            'statusOptions' => User::getStatusOptions()
        ]);
    }
} 