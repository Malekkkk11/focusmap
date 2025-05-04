@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Group Goals</h2>
                <a href="{{ route('group-goals.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Create Group Goal
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">My Group Goals</h5>
                </div>
                <div class="card-body">
                    @forelse($participatingGoals as $goal)
                        <div class="group-goal-card mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="card-title mb-1">
                                                <a href="{{ route('group-goals.show', $goal) }}" class="text-decoration-none">
                                                    {{ $goal->title }}
                                                </a>
                                            </h5>
                                            <p class="text-muted small mb-2">
                                                Created by {{ $goal->creator->name }} | 
                                                {{ $goal->participants_count }} participants
                                            </p>
                                        </div>
                                        <span class="badge bg-primary">{{ number_format($goal->average_progress) }}% avg</span>
                                    </div>
                                    
                                    <p class="card-text">{{ Str::limit($goal->description, 100) }}</p>
                                    
                                    <div class="progress mb-2" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" 
                                            style="width: {{ $goal->pivot->progress }}%">
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            Your progress: {{ $goal->pivot->progress }}%
                                        </small>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="updateProgress({{ $goal->id }})">
                                                Update Progress
                                            </button>
                                            @if(!$goal->userIsAdmin(auth()->user()))
                                                <form action="{{ route('group-goals.leave', $goal) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Are you sure you want to leave this group goal?')">
                                                        Leave
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center my-4">
                            You are not participating in any group goals yet.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Available Group Goals</h5>
                </div>
                <div class="card-body">
                    @forelse($availableGoals as $goal)
                        <div class="group-goal-card mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title mb-1">{{ $goal->title }}</h6>
                                    <p class="text-muted small mb-2">
                                        {{ $goal->participants_count }} participants
                                        @if($goal->participants_limit)
                                            (max: {{ $goal->participants_limit }})
                                        @endif
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            Ends: {{ $goal->end_date->format('M d, Y') }}
                                        </small>
                                        <form action="{{ route('group-goals.join', $goal) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">Join</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center my-4">
                            No available group goals at the moment.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Update Modal -->
<div class="modal fade" id="progressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Progress</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="progressForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Your Progress (%)</label>
                        <input type="number" class="form-control" name="progress" 
                               min="0" max="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateProgress(goalId) {
    const modal = new bootstrap.Modal(document.getElementById('progressModal'));
    const form = document.getElementById('progressForm');
    form.action = `/group-goals/${goalId}/progress`;
    modal.show();
}
</script>
@endpush
@endsection