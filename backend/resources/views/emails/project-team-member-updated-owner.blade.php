<x-mail.layout>
    <x-slot:greeting>
        {{ $greeting }}
    </x-slot>

    <div>
        @if($oldStatus === 'removed')
            <p>A team member has been removed from your project:</p>
        @else
            <p>A team member's status has been updated in your project:</p>
        @endif
        
        <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <p><strong>Project:</strong> {{ $teamMember->project->name }}</p>
            <p><strong>Team Member:</strong> {{ $teamMember->professional->user->first_name }} {{ $teamMember->professional->user->last_name }}</p>
            <p><strong>Role:</strong> {{ ucwords(str_replace('_', ' ', $teamMember->role)) }}</p>
            @if($oldStatus !== 'removed')
                <p><strong>Previous Status:</strong> {{ ucfirst($oldStatus) }}</p>
                <p><strong>New Status:</strong> {{ ucfirst($teamMember->status) }}</p>
            @endif
        </div>

        <p class="mt-4">You can manage project team members from your dashboard.</p>
    </div>

    <x-slot:closing>
        {{ $closing }}
    </x-slot>
</x-mail.layout> 