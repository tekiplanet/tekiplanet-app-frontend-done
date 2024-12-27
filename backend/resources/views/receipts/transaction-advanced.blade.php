<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transaction Receipt</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #333;
            font-size: 14px;
            line-height: 1.4;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            border: 2px solid #ddd;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #141F78;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 15px;
        }
        .receipt-title {
            font-size: 24px;
            color: #141F78;
            margin: 10px 0;
            font-weight: bold;
        }
        .receipt-number {
            color: #666;
            font-size: 16px;
        }
        .status-stamp {
            position: absolute;
            top: 100px;
            right: 50px;
            transform: rotate(-15deg);
            font-size: 24px;
            font-weight: bold;
            padding: 10px 20px;
            border: 3px solid;
            border-radius: 10px;
            opacity: 0.5;
        }
        .status-completed {
            color: #059669;
            border-color: #059669;
        }
        .status-pending {
            color: #D97706;
            border-color: #D97706;
        }
        .status-failed {
            color: #DC2626;
            border-color: #DC2626;
        }
        .status-cancelled {
            color: #4B5563;
            border-color: #4B5563;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #141F78;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .label {
            color: #666;
            font-size: 12px;
            margin-bottom: 3px;
        }
        .value {
            font-weight: 600;
            color: #333;
        }
        .amount {
            font-size: 20px;
            font-weight: bold;
            color: #141F78;
        }
        .credit {
            color: #059669;
        }
        .debit {
            color: #DC2626;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <img src="{{ public_path('images/logo.png') }}" alt="TekiPlanet" class="logo">
            <div class="receipt-title">Transaction Receipt</div>
            <div class="receipt-number">Reference: {{ $transaction->reference_number }}</div>
        </div>

        <!-- Status Stamp -->
        <div class="status-stamp status-{{ $transaction->status }}">
            {{ strtoupper($transaction->status) }}
        </div>

        <div class="section">
            <div class="section-title">Transaction Details</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Transaction Date</div>
                    <div class="value">{{ $transaction->created_at->format('M d, Y h:i A') }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Transaction Type</div>
                    <div class="value">{{ ucfirst($transaction->type) }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Amount</div>
                    <div class="value amount {{ $transaction->type === 'credit' ? 'credit' : 'debit' }}">
                        {{ $transaction->type === 'credit' ? '+' : '-' }} ${{ number_format($transaction->amount, 2) }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="label">Status</div>
                    <div class="value">{{ ucfirst($transaction->status) }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Account Information</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Account Holder</div>
                    <div class="value">{{ $transaction->user->full_name }}</div>
                </div>
                <div class="info-item">
                    <div class="label">Email</div>
                    <div class="value">{{ $transaction->user->email }}</div>
                </div>
                @if($transaction->payment_method)
                <div class="info-item">
                    <div class="label">Payment Method</div>
                    <div class="value">{{ $transaction->payment_method }}</div>
                </div>
                @endif
                <div class="info-item">
                    <div class="label">Current Balance</div>
                    <div class="value">${{ number_format($transaction->user->wallet_balance, 2) }}</div>
                </div>
            </div>
        </div>

        @if($transaction->description)
        <div class="section">
            <div class="section-title">Description</div>
            <div class="value">{{ $transaction->description }}</div>
        </div>
        @endif

        <div class="footer">
            <p>This is an electronically generated receipt. No signature is required.</p>
            <p>© {{ date('Y') }} TekiPlanet. All rights reserved.</p>
            <p>For any queries, please contact our support team.</p>
        </div>
    </div>
</body>
</html> 