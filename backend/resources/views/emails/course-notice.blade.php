@component('mail::message')
# {{ $courseNotice->title }}

{!! $courseNotice->content !!}

@if($courseNotice->is_important)
**This is an important notice. Please read carefully.**
@endif

Priority: {{ ucfirst($courseNotice->priority) }}

@component('mail::button', ['url' => config('app.url') . '/dashboard/courses/' . $courseNotice->course_id])
View Course
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 