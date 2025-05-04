@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Goal Map</h5>
                    <a href="{{ route('goals.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Add Goal
                    </a>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 600px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Goals with Location</h5>
                </div>
                <div class="card-body">
                    <div class="goals-list">
                        @forelse($goals as $goal)
                            <div class="goal-item mb-3" data-id="{{ $goal->id }}">
                                <h6 class="mb-1">{{ $goal->title }}</h6>
                                <p class="text-muted small mb-2">{{ $goal->location_name }}</p>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" 
                                        style="width: {{ $goal->progress }}%">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No goals with location yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const map = L.map('map').setView([0, 0], 2);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add markers for each goal
    const goals = @json($goals);
    const markers = {};
    
    goals.forEach(goal => {
        const marker = L.marker([goal.latitude, goal.longitude])
            .addTo(map)
            .bindPopup(`
                <strong>${goal.title}</strong><br>
                ${goal.location_name}<br>
                Progress: ${goal.progress}%<br>
                <a href="/goals/${goal.id}" class="btn btn-sm btn-primary mt-2">View Details</a>
            `);
        
        markers[goal.id] = marker;
    });

    // Highlight marker when hovering over goal in list
    document.querySelectorAll('.goal-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            const goalId = this.dataset.id;
            const marker = markers[goalId];
            if (marker) {
                marker.openPopup();
                this.classList.add('bg-light');
            }
        });

        item.addEventListener('mouseleave', function() {
            const goalId = this.dataset.id;
            const marker = markers[goalId];
            if (marker) {
                marker.closePopup();
                this.classList.remove('bg-light');
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.goal-item {
    padding: 10px;
    border-radius: 5px;
    transition: background-color 0.2s;
    cursor: pointer;
}

.goal-item:hover {
    background-color: #f8f9fa;
}
</style>
@endpush
@endsection