<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
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

    public function show(User $user)
    {
        // Load relationships
        $user->load(['businessProfile', 'professional']);
        
        // Get recent transactions
        $transactions = $user->transactions()
            ->latest()
            ->paginate(10);

        // Get wallet statistics
        $stats = [
            'total_credits' => $user->transactions()->credits()->sum('amount'),
            'total_debits' => $user->transactions()->debits()->sum('amount'),
            'current_balance' => $user->wallet_balance,
        ];

        return view('admin.users.show', compact('user', 'transactions', 'stats'));
    }

    // Add method to handle new transactions
    public function createTransaction(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:credit,debit',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'payment_method' => 'required|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Generate reference number
        $validated['reference_number'] = 'TXN-' . strtoupper(uniqid());
        $validated['status'] = 'completed';
        $validated['user_id'] = $user->id;

        // Create transaction
        $transaction = Transaction::create($validated);

        // Update user's wallet balance
        if ($validated['type'] === 'credit') {
            $user->increment('wallet_balance', $validated['amount']);
        } else {
            $user->decrement('wallet_balance', $validated['amount']);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Transaction created successfully');
    }
} 