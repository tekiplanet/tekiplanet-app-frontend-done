<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Setting;
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

    public function show(Request $request, User $user)
    {
        // Load relationships
        $user->load(['businessProfile', 'professional']);
        
        // Get transactions with search
        $transactions = $user->transactions()
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhere('amount', 'like', "%{$search}%");
                });
            })
            ->when($request->type, function($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Get wallet statistics
        $stats = [
            'total_credits' => $user->transactions()->credits()->sum('amount'),
            'total_debits' => $user->transactions()->debits()->sum('amount'),
            'current_balance' => $user->wallet_balance,
        ];

        // Get currency settings
        $currency = [
            'code' => Setting::getSetting('default_currency', 'USD'),
            'symbol' => Setting::getSetting('currency_symbol', '$')
        ];

        return view('admin.users.show', compact('user', 'transactions', 'stats', 'currency'));
    }

    // Add method to handle new transactions
    public function createTransaction(Request $request, User $user)
    {
        try {
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
                if ($user->wallet_balance < $validated['amount']) {
                    throw new \Exception('Insufficient balance for debit transaction.');
                }
                $user->decrement('wallet_balance', $validated['amount']);
            }

            return redirect()
                ->route('admin.users.show', $user)
                ->with('notify', [
                    'type' => 'success',
                    'message' => 'Transaction created successfully'
                ]);

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.users.show', $user)
                ->with('notify', [
                    'type' => 'error',
                    'message' => $e->getMessage() ?: 'Failed to create transaction'
                ]);
        }
    }
} 