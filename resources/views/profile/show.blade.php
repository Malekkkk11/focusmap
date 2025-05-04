@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <x-streak-status :user="$user" />

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Your Achievements</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @forelse($user->badges->sortByDesc('pivot.earned_at') as $badge)
                            <div class="col-md-4">
                                <div class="achievement-card">
                                    <div class="achievement-icon mb-3">
                                        <i class="bi bi-{{ $badge->icon }} display-4 text-primary"></i>
                                    </div>
                                    <h6 class="mb-2">{{ $badge->name }}</h6>
                                    <p class="text-muted small mb-2">{{ $badge->description }}</p>
                                    <small class="text-muted">Earned {{ $badge->pivot->earned_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-4">
                                    <i class="bi bi-trophy display-4 text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Start achieving goals to earn badges!</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Stats Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Goal Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-card text-center p-3">
                                <h3 class="mb-1">{{ $user->goals->count() }}</h3>
                                <small class="text-muted">Total Goals</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card text-center p-3">
                                <h3 class="mb-1">{{ $user->goals->where('progress', 100)->count() }}</h3>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card text-center p-3">
                                <h3 class="mb-1">{{ $user->badges->count() }}</h3>
                                <small class="text-muted">Badges Earned</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card text-center p-3">
                                <h3 class="mb-1">{{ $user->goals->where('progress', '>', 0)->where('progress', '<', 100)->count() }}</h3>
                                <small class="text-muted">In Progress</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Category Distribution -->
                    <h6 class="mb-3">Goals by Category</h6>
                    @if($user->goals->count())
                        @foreach($user->goals->groupBy('category') as $category => $goals)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span>{{ $category }}</span>
                                    <span class="text-muted">{{ $goals->count() }}</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" role="progressbar" 
                                        style="width: {{ ($goals->count() / max($user->goals->count(), 1)) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No goals available.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($recentActivity as $activity)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    @if($activity['type'] === 'badge')
                                        <i class="bi bi-award text-warning me-2"></i>
                                    @elseif($activity['type'] === 'goal')
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                    @else
                                        <i class="bi bi-pencil text-primary me-2"></i>
                                    @endif
                                    <div>
                                        <p class="mb-1">{{ $activity['description'] }}</p>
                                        <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.achievement-card {
    background: #f8f9fa;
    border-radius: 1rem;
    padding: 1.5rem;
    text-align: center;
    height: 100%;
    transition: transform 0.2s;
}

.achievement-card:hover {
    transform: translateY(-5px);
}

.achievement-icon {
    background: rgba(var(--bs-primary-rgb), 0.1);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.stat-card {
    background: #f8f9fa;
    border-radius: 0.5rem;
    transition: background-color 0.2s;
}

.stat-card:hover {
    background: rgba(var(--bs-primary-rgb), 0.1);
}
</style>
@endpush
@endsection
