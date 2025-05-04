@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $groupGoal->title }}</h5>
                        <span class="badge bg-primary">{{ $groupGoal->category }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $groupGoal->description }}</p>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="mb-2">Duration</h6>
                            <p class="text-muted">
                                {{ $groupGoal->start_date->format('M d, Y') }} - 
                                {{ $groupGoal->end_date->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-2">Created by</h6>
                            <p class="text-muted">{{ $groupGoal->creator->name }}</p>
                        </div>
                    </div>

                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar" role="progressbar" 
                            style="width: {{ $groupGoal->average_progress }}%">
                            {{ number_format($groupGoal->average_progress) }}% Average Progress
                        </div>
                    </div>

                    @if($groupGoal->userIsParticipant(auth()->user()))
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Your Progress:</strong> {{ $groupGoal->participants->find(auth()->id())->pivot->progress }}%
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" 
                                        onclick="updateProgress({{ $groupGoal->id }})">
                                    Update Progress
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Participants ({{ $groupGoal->participants_count }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($groupGoal->participants as $participant)
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            {{ $participant->name }}
                                            @if($participant->pivot->is_admin)
                                                <span class="badge bg-secondary">Admin</span>
                                            @endif
                                        </h6>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar" role="progressbar" 
                                                style="width: {{ $participant->pivot->progress }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            Progress: {{ $participant->pivot->progress }}%
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                @if(!$groupGoal->userIsParticipant(auth()->user()) && $groupGoal->hasAvailableSpots())
                    <div class="card-footer">
                        <form action="{{ route('group-goals.join', $groupGoal) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">Join This Goal</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Goal Stats</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Participants</h6>
                        <p class="mb-0">
                            {{ $groupGoal->participants_count }}
                            @if($groupGoal->participants_limit)
                                / {{ $groupGoal->participants_limit }}
                            @else
                                <span class="text-muted">(No limit)</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <h6>Duration</h6>
                        <p class="mb-0">{{ $groupGoal->start_date->diffInDays($groupGoal->end_date) }} days</p>
                    </div>
                    <div>
                        <h6>Time Remaining</h6>
                        <p class="mb-0">{{ now()->diffInDays($groupGoal->end_date) }} days</p>
                    </div>
                </div>
            </div>

            @if($groupGoal->userIsParticipant(auth()->user()) && !$groupGoal->userIsAdmin(auth()->user()))
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('group-goals.leave', $groupGoal) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Are you sure you want to leave this group goal?')">
                                Leave Goal
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Progress Update Modal -->
<div class="modal fade" id="progressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Your Progress</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="progressForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Progress (%)</label>
                        <input type="number" class="form-control" name="progress" 
                               min="0" max="100" required 
                               value="{{ $groupGoal->participants->find(auth()->id())->pivot->progress ?? 0 }}">
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