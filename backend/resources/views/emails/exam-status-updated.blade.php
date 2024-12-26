<x-mail.layout>
    <h1>Exam Status Update</h1>
    
    <p>Hello {{ $userExam->user->name }},</p>

    <p>Your exam status for <strong>{{ $userExam->exam->title }}</strong> has been updated.</p>

    <div style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <p style="margin: 5px 0;"><strong>Status:</strong> {{ str_replace('_', ' ', ucfirst($userExam->status)) }}</p>
        @if($userExam->score !== null)
            <p style="margin: 5px 0;">
                <strong>Score:</strong> {{ $userExam->score }}/{{ $userExam->total_score }}
                ({{ round(($userExam->score / $userExam->total_score) * 100) }}%)
            </p>
        @endif
    </div>

    <p>You can view your exam details by logging into your account.</p>
</x-mail.layout> 