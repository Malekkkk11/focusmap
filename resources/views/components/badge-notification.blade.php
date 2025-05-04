<div id="badgeNotification" class="toast position-fixed bottom-0 end-0 m-4" role="alert" style="z-index: 1050;">
    <div class="toast-header bg-primary text-white">
        <i class="bi bi-award me-2"></i>
        <strong class="me-auto">New Badge Earned!</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body">
        <div class="d-flex align-items-center">
            <div class="badge-icon me-3">
                <i class="bi bi-{{ $badge->icon }} display-6"></i>
            </div>
            <div>
                <h6 class="mb-1">{{ $badge->name }}</h6>
                <p class="mb-0 small">{{ $badge->description }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toast = new bootstrap.Toast(document.getElementById('badgeNotification'));
        toast.show();
    });
</script>
@endpush