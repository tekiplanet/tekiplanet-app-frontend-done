<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\AdminRole;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\BankAccount;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();

        // Return different dashboard views based on role
        return match($admin->role) {
            AdminRole::SUPER_ADMIN => $this->superAdminDashboard(),
            AdminRole::ADMIN => $this->adminDashboard(),
            AdminRole::SALES => $this->salesDashboard(),
            AdminRole::FINANCE => $this->financeDashboard(),
            AdminRole::TUTOR => $this->tutorDashboard(),
            AdminRole::MANAGEMENT => $this->managementDashboard(),
            default => abort(403, 'Unauthorized role'),
        };
    }

    private function superAdminDashboard()
    {
        // Fetch data for super admin dashboard
        return view('admin.dashboard.super_admin');
    }

    private function adminDashboard()
    {
        // Fetch data for admin dashboard
        return view('admin.dashboard.admin');
    }

    private function salesDashboard()
    {
        // Fetch data specific to sales role
        $recentOrders = Order::latest()->take(5)->get();
        $totalSales = Order::sum('total');
        
        // Instead of using withCount, let's just get basic product stats
        $productStats = Product::latest()->take(5)->get();
        
        return view('admin.dashboard.sales', compact(
            'recentOrders',
            'totalSales',
            'productStats'
        ));
    }

    private function financeDashboard()
    {
        $totalRevenue = Transaction::where('type', 'credit')->sum('amount');
        $monthlyRevenue = Transaction::where('type', 'credit')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        $pendingTransactions = Transaction::where('status', 'pending')->count();
        $activeBankAccounts = BankAccount::where('is_verified', true)->count();
        $recentTransactions = Transaction::latest()->take(5)->get();
        $bankAccounts = BankAccount::latest()->take(5)->get();

        return view('admin.dashboard.finance', compact(
            'totalRevenue',
            'monthlyRevenue',
            'pendingTransactions',
            'activeBankAccounts',
            'recentTransactions',
            'bankAccounts'
        ));
    }

    private function tutorDashboard()
    {
        // Fetch tutor specific data
        return view('admin.dashboard.tutor');
    }

    private function managementDashboard()
    {
        // Fetch management specific data
        return view('admin.dashboard.management');
    }
} 