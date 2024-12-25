<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        // You can also apply middleware in the constructor
        $this->middleware('admin.roles:super_admin,admin');
    }

    public function index()
    {
        // Only super_admin and admin can access this
        return view('admin.users.index');
    }

    public function create()
    {
        // Only super_admin can create new admins
        $this->middleware('admin.role:super_admin');
        return view('admin.users.create');
    }
} 