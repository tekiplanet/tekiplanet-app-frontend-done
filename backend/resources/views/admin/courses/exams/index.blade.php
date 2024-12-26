@extends('admin.layouts.app')

@section('content')
<div class="container px-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Course Exams - {{ $course->title }}
        </h2>
        <a href="{{ route('admin.courses.exams.create', $course) }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Create Exam
        </a>
    </div>

    <!-- Exams Table -->
    <div class="w-full overflow-hidden rounded-lg shadow-md">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b">
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Duration</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($exams as $exam)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">
                                {{ $exam->title }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $exam->date->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $exam->duration }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $exam->type === 'multiple_choice' ? 'bg-blue-100 text-blue-800' : 
                                       ($exam->type === 'true_false' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ str_replace('_', ' ', ucfirst($exam->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $exam->status === 'upcoming' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($exam->status === 'ongoing' ? 'bg-green-100 text-green-800' : 
                                       ($exam->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ ucfirst($exam->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('admin.courses.exams.show', [$course, $exam]) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        View
                                    </a>
                                    <a href="{{ route('admin.courses.exams.edit', [$course, $exam]) }}" 
                                       class="text-yellow-600 hover:text-yellow-900">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.courses.exams.destroy', [$course, $exam]) }}" 
                                          method="POST" 
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure you want to delete this exam?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                                No exams found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $exams->links() }}
        </div>
    </div>
</div>
@endsection 