<x-mail.layout>
    <x-slot:greeting>
        {{ $greeting }}
    </x-slot>

    <p>You have received a new message regarding your quote.</p>

    <p><strong>Message:</strong><br>
    {{ $message->message }}</p>

    <p>Click the button below to view and reply to this message:</p>

    <a href="{{ url('/quotes/' . $message->quote_id) }}" class="button">
        View Message
    </a>

    <x-slot:closing>
        {{ $closing }}
    </x-slot>
</x-mail.layout> 