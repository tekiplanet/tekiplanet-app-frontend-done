<script>
async function updateApplicationStatus(url, status, action) {
    const result = await Swal.fire({
        title: `${action} Application`,
        text: `Are you sure you want to ${action.toLowerCase()} this application?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: action,
        cancelButtonText: 'Cancel',
        confirmButtonColor: status === 'approved' ? '#10B981' : '#EF4444',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        _method: 'PATCH',
                        status: status
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to update application status');
                }

                return response.json();
            } catch (error) {
                Swal.showValidationMessage(`Request failed: ${error}`);
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    });

    if (result.isConfirmed) {
        await Swal.fire({
            title: 'Success!',
            text: 'Application status updated successfully',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
        
        // Reload the page to show updated status
        window.location.reload();
    }
}
</script> 