<x-mail.layout>
    <div>
        <h2 class="text-xl font-bold mb-4">Project Status Updated</h2>
        
        <div class="text-gray-600 dark:text-gray-400">
            <p>The status of your project has been updated:</p>
            
            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p><strong>Project Name:</strong> {{ $project->name }}</p>
                <p><strong>Previous Status:</strong> {{ ucfirst($oldStatus) }}</p>
                <p><strong>New Status:</strong> {{ ucfirst($newStatus) }}</p>
                @if($notes)
                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900 rounded">
                        <strong class="text-blue-700 dark:text-blue-200">Update Notes:</strong>
                        <p class="mt-1 text-blue-600 dark:text-blue-300">{{ $notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ config('app.url') }}/dashboard/projects/{{ $project->id }}" 
               class="button">
                View Project Details
            </a>
        </div>
    </div>
</x-mail.layout> 