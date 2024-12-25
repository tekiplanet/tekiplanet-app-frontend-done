<x-mail.layout>
    <x-slot:greeting>
        Hello {{ $enrollment->user->first_name }},
    </x-slot:greeting>

    <p>Your enrollment status for the course "{{ $course->title }}" has been updated.</p>

    <div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 6px;" class="info-box">
        <p style="margin: 0;"><strong>{{ $fieldLabel }}:</strong></p>
        <p style="margin: 5px 0;">Changed from: {{ str_replace('_', ' ', ucfirst($oldValue)) }}</p>
        <p style="margin: 5px 0;">To: {{ str_replace('_', ' ', ucfirst($newValue)) }}</p>
    </div>

    <x-slot:closing>
        If you have any questions, please don't hesitate to contact us.
    </x-slot:closing>
</x-mail.layout> 