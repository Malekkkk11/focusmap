@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Goals</h1>
        <a href="{{ route('goals.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create New Goal
        </a>
    </div>

    <div class="row g-4">
        @forelse($goals as $goal)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title mb-0">{{ $goal->title }}</h5>
                            <span class="badge bg-{{ $goal->progress == 100 ? 'success' : 'primary' }}">
                                {{ number_format($goal->progress) }}%
                            </span>
                        </div>
                        
                        <p class="card-text text-muted mb-3">{{ Str::limit($goal->description, 100) }}</p>
                        
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar" role="progressbar" 
                                style="width: {{ $goal->progress }}%"
                                aria-valuenow="{{ $goal->progress }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <i class="bi bi-calendar-event"></i>
                                {{ $goal->deadline ? $goal->deadline->format('M d, Y') : 'No deadline' }}
                            </div>
                            <div>
                                @if($goal->location_name)
                                    <span class="badge bg-light text-dark me-2" title="Location">
                                        <i class="bi bi-geo-alt"></i> {{ $goal->location_name }}
                                    </span>
                                @endif
                                <span class="badge bg-light text-dark" title="Category">
                                    <i class="bi bi-tag"></i> {{ $goal->category }}
                                </span>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-light text-dark" title="Steps">
                                    <i class="bi bi-list-check"></i> 
                                    {{ $goal->steps->count() }} steps
                                </span>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('goals.edit', $goal) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-plus display-1 text-muted mb-3"></i>
                    <h3 class="text-muted">No Goals Yet</h3>
                    <p class="text-muted mb-3">Start tracking your goals by creating your first goal!</p>
                    <a href="{{ route('goals.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Create Your First Goal
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.progress {
    border-radius: 0.5rem;
}

.progress-bar {
    transition: width 0.6s ease;
}

.badge {
    font-weight: 500;
}
</style>
@endpush