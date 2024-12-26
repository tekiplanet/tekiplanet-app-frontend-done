<div>
    <h2 class="text-xl font-bold mb-4">Account Status Update</h2>
    
    <div class="text-gray-600 dark:text-gray-400">
        <p>Your account status has been updated to <strong>{{ $status }}</strong>.</p>
        
        @if($actionText && $actionUrl)
            <a href="{{ $actionUrl }}" class="button">
                {{ $actionText }}
            </a>
        @endif
    </div>

    <div class="mt-8 text-sm text-gray-500">
        <p>If you believe this change was made in error, please contact our support team.</p>
    </div>
</div> 