@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                Course Exams
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $course->title }}
            </p>
        </div>
        <!-- Create Exam Button -->
        <button type="button"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                onclick="window.location.href='{{ route('admin.courses.exams.create', $course) }}'">
            Create Exam
        </button>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($exams as $exam)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.courses.exams.show', [$course, $exam]) }}" 
                               class="text-blue-600 hover:text-blue-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No exams found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $exams->links() }}
    </div>
</div>
@endsection 