<script>
// Bulk Actions
const bulkActionForm = document.getElementById('bulkActionForm');
const bulkActionSelect = document.getElementById('bulkAction');
const bulkStatusSelect = document.getElementById('bulkStatus');
const bulkScoreInputs = document.getElementById('bulkScoreInputs');
const selectAllCheckbox = document.getElementById('selectAll');
const applyBulkAction = document.getElementById('applyBulkAction');

// Individual Action Modal
const actionModal = document.getElementById('actionModal');
const individualActionForm = document.getElementById('individualActionForm');
const userExamIdInput = document.getElementById('userExamId');

// Handle bulk action selection
bulkActionSelect.addEventListener('change', function() {
    bulkStatusSelect.classList.add('hidden');
    bulkScoreInputs.classList.add('hidden');
    
    if (this.value === 'status') {
        bulkStatusSelect.classList.remove('hidden');
    } else if (this.value === 'score') {
        bulkScoreInputs.classList.remove('hidden');
    }
    
    validateBulkForm();
});

// Handle select all
selectAllCheckbox.addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    validateBulkForm();
});

// Handle individual checkbox changes
document.querySelectorAll('.user-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', validateBulkForm);
});

function validateBulkForm() {
    const hasSelection = document.querySelector('.user-checkbox:checked');
    const hasAction = bulkActionSelect.value !== '';
    applyBulkAction.disabled = !hasSelection || !hasAction;
}

// Handle bulk action submission
bulkActionForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    const action = bulkActionSelect.value;
    
    let data = {
        user_exams: selectedUsers,
        action: action
    };
    
    if (action === 'status') {
        data.status = bulkStatusSelect.value;
    } else if (action === 'score') {
        data.score = document.getElementById('bulkScore').value;
        data.total_score = document.getElementById('bulkTotalScore').value;
    }
    
    try {
        const response = await submitAction('bulk', data);
        if (response.success) {
            showNotification('', 'Successfully updated participants', 'success');
            setTimeout(() => window.location.reload(), 1000);
        }
    } catch (error) {
        showNotification('', error.message || 'An error occurred', 'error');
    }
});

// Individual action modal functions
function openActionModal(userExamId) {
    userExamIdInput.value = userExamId;
    actionModal.classList.remove('hidden');
}

function closeActionModal() {
    actionModal.classList.add('hidden');
}

// Handle individual action submission
document.getElementById('saveIndividualAction').addEventListener('click', async function() {
    const data = {
        status: document.getElementById('individualStatus').value,
        score: document.getElementById('individualScore').value,
        total_score: document.getElementById('individualTotalScore').value
    };
    
    try {
        const response = await submitAction('individual', data, userExamIdInput.value);
        if (response.success) {
            showNotification('', 'Successfully updated participant', 'success');
            closeActionModal();
            setTimeout(() => window.location.reload(), 1000);
        }
    } catch (error) {
        showNotification('', error.message || 'An error occurred', 'error');
    }
});

async function submitAction(type, data, userExamId = null) {
    const url = type === 'bulk' 
        ? `/admin/courses/{{ $course->id }}/exams/{{ $exam->id }}/participants/bulk-update`
        : `/admin/courses/{{ $course->id }}/exams/{{ $exam->id }}/participants/${userExamId}`;
    
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    });
    
    if (!response.ok) {
        const text = await response.text();
        try {
            const json = JSON.parse(text);
            throw new Error(json.message || `HTTP error! status: ${response.status}`);
        } catch (e) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
    }
    
    return response.json();
}
</script> 