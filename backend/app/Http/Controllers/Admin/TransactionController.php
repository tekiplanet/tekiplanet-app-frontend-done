<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class TransactionController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = Transaction::with(['user'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reference_number', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            });

        $transactions = $query->latest()->paginate(15);
        
        // Add this for debugging
        \Log::info('Transactions with users:', [
            'sample_transaction' => $transactions->first(),
            'has_user' => $transactions->first()->user ? 'yes' : 'no',
            'user_details' => $transactions->first()->user
        ]);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,cancelled',
            'notes' => 'nullable|string'
        ]);

        try {
            $transaction->update($validated);

            // Send notification to user
            $this->notificationService->send([
                'type' => 'transaction_status_updated',
                'title' => 'Transaction Status Updated',
                'message' => "Your transaction #{$transaction->reference_number} status has been updated to " . ucfirst($validated['status']),
                'action_url' => "/transactions/{$transaction->id}"
            ], $transaction->user);

            return response()->json([
                'success' => true,
                'message' => 'Transaction status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction status: ' . $e->getMessage()
            ], 500);
        }
    }
} 