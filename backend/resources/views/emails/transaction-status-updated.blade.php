<x-mail.layout>
    <x-slot name="greeting">
        Hello {{ $user->first_name }},
    </x-slot>

    <p>Your transaction with reference number <strong>{{ $transaction->reference_number }}</strong> has been updated.</p>

    <p>Details:</p>
    <ul>
        <li>Status: <strong>{{ ucfirst($transaction->status) }}</strong></li>
        <li>Amount: <strong>{{ number_format($transaction->amount, 2) }}</strong></li>
        <li>Type: <strong>{{ ucfirst($transaction->type) }}</strong></li>
        @if(isset($transaction->notes['status_update']['note']))
            <li>Note: {{ $transaction->notes['status_update']['note'] }}</li>
        @endif
    </ul>

    <p>You can view the complete transaction details in your account.</p>

    <x-slot name="closing">
        Best regards,<br>
        The TekiPlanet Team
    </x-slot>
</x-mail.layout> 