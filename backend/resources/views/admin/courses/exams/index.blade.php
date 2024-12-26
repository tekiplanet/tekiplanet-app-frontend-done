@extends('admin.layouts.app')

@section('content')
<div x-data="{ 
    showEditModal: false,
    examId: null,
    examData: {
        title: '',
        description: '',
        date: '',
        duration: '',
        duration_minutes: '',
        type: '',
        difficulty: '',
        total_questions: '',
        pass_percentage: '',
        is_mandatory: false
    },
    loading: false,
    initializeExam(exam) {
        this.examId = exam.id;
        this.examData = {
            title: exam.title,
            description: exam.description || '',
            date: exam.date,
            duration: exam.duration,
            duration_minutes: exam.duration_minutes,
            type: exam.type,
            difficulty: exam.difficulty,
            total_questions: exam.total_questions,
            pass_percentage: exam.pass_percentage,
            is_mandatory: exam.is_mandatory
        };
        this.showEditModal = true;
    }
}" class="container px-6 mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Course Exams - {{ $course->title }}
        </h2>
        <a href="{{ route('admin.courses.exams.create', $course) }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Create Exam
        </a>
    </div>

    <!-- Search/Filter Section -->
    <div class="mb-6 bg-white rounded-lg shadow-md dark:bg-gray-800 p-4">
        <form action="{{ route('admin.courses.exams.index', $course) }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                       placeholder="Search exams...">
            </div>

            <div class="w-full md:w-48">
                <select name="type" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">All Types</option>
                    <option value="multiple_choice" {{ request('type') === 'multiple_choice' ? 'selected' : '' }}>
                        Multiple Choice
                    </option>
                    <option value="true_false" {{ request('type') === 'true_false' ? 'selected' : '' }}>
                        True/False
                    </option>
                    <option value="mixed" {{ request('type') === 'mixed' ? 'selected' : '' }}>
                        Mixed
                    </option>
                </select>
            </div>

            <div class="w-full md:w-48">
                <select name="status" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="missed" {{ request('status') === 'missed' ? 'selected' : '' }}>Missed</option>
                </select>
            </div>

            <div class="w-full md:w-48">
                <select name="sort_by" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="date" {{ request('sort_by') === 'date' ? 'selected' : '' }}>Sort by Date</option>
                    <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Sort by Title</option>
                    <option value="type" {{ request('sort_by') === 'type' ? 'selected' : '' }}>Sort by Type</option>
                    <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Sort by Status</option>
                </select>
            </div>

            <div class="w-full md:w-48">
                <select name="sort_order" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
            </div>

            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Search
            </button>
        </form>
    </div>

    <!-- Exams List -->
    <div class="w-full rounded-lg shadow-md">
        <!-- Desktop Table (hidden on mobile) -->
        <div class="hidden md:block w-full overflow-x-auto">
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
                            <td class="px-4 py-3">{{ $exam->title }}</td>
                            <td class="px-4 py-3">{{ $exam->date->format('M d, Y') }}</td>
                            <td class="px-4 py-3">{{ $exam->duration }}</td>
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
                                       class="text-blue-600 hover:text-blue-900">View</a>
                                    <button @click="initializeExam({{ $exam->toJson() }})" 
                                            class="text-yellow-600 hover:text-yellow-900">Edit</button>
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

        <!-- Mobile Cards (hidden on desktop) -->
        <div class="md:hidden">
            @forelse($exams as $exam)
                <div class="bg-white p-4 border-b border-gray-200 space-y-3">
                    <div class="flex justify-between items-start">
                        <h3 class="font-semibold text-gray-900">{{ $exam->title }}</h3>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $exam->status === 'upcoming' ? 'bg-yellow-100 text-yellow-800' : 
                               ($exam->status === 'ongoing' ? 'bg-green-100 text-green-800' : 
                               ($exam->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                            {{ ucfirst($exam->status) }}
                        </span>
                    </div>

                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Date:</span>
                            <span>{{ $exam->date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Duration:</span>
                            <span>{{ $exam->duration }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Type:</span>
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $exam->type === 'multiple_choice' ? 'bg-blue-100 text-blue-800' : 
                                   ($exam->type === 'true_false' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ str_replace('_', ' ', ucfirst($exam->type)) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                        <a href="{{ route('admin.courses.exams.show', [$course, $exam]) }}" 
                           class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            View
                        </a>
                        <button @click="initializeExam({{ $exam->toJson() }})" 
                                class="px-3 py-1 text-sm bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            Edit
                        </button>
                        <form action="{{ route('admin.courses.exams.destroy', [$course, $exam]) }}" 
                              method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-3 py-1 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700"
                                    onclick="return confirm('Are you sure you want to delete this exam?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-gray-500">
                    No exams found
                </div>
            @endforelse
        </div>

        <div class="px-4 py-3 border-t">
            {{ $exams->links() }}
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-gray-800"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <form @submit.prevent="submitForm()" class="p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Exam</h3>
                    </div>

                    <div class="space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                            <input type="text" 
                                   x-model="examData.title"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                            <input type="date" 
                                   x-model="examData.date"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Duration -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration</label>
                                <input type="text" 
                                       x-model="examData.duration"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minutes</label>
                                <input type="number" 
                                       x-model="examData.duration_minutes"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                            <select x-model="examData.type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="mixed">Mixed</option>
                            </select>
                        </div>

                        <!-- Difficulty -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Difficulty</label>
                            <select x-model="examData.difficulty"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>

                        <!-- Questions and Pass Percentage -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Questions</label>
                                <input type="number" 
                                       x-model="examData.total_questions"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pass Percentage</label>
                                <input type="number" 
                                       x-model="examData.pass_percentage"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <textarea x-model="examData.description"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>

                        <!-- Is Mandatory -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   x-model="examData.is_mandatory"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mandatory Exam</label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button"
                                @click="showEditModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                :disabled="loading"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 flex items-center gap-2">
                            <span x-show="loading" class="inline-block animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
                            <span x-text="loading ? 'Saving...' : 'Save Changes'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function submitForm() {
    if (this.loading) return;
    
    this.loading = true;
    
    fetch(`{{ route('admin.courses.exams.index', $course) }}/${this.examId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-HTTP-Method-Override': 'PUT'
        },
        body: JSON.stringify(this.examData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('', 'Exam updated successfully', 'success');
            window.location.reload();
        } else {
            showNotification('', 'Failed to update exam', 'error');
        }
    })
    .catch(error => {
        showNotification('', 'An error occurred', 'error');
    })
    .finally(() => {
        this.loading = false;
        this.showEditModal = false;
    });
}
</script>
@endpush
@endsection 