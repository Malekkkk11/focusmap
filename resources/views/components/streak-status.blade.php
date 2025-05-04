<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <div class="streak-icon me-3">
                        <i class="bi bi-calendar-check display-5 text-primary"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $journalStreak }}</h3>
                        <small class="text-muted">Day Streak</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <div class="streak-icon me-3">
                        <i class="bi bi-lightning display-5 text-warning"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $activeGoals }}</h3>
                        <small class="text-muted">Active Goals</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <div class="streak-icon me-3">
                        <i class="bi bi-graph-up display-5 text-success"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $weeklyProgress }}%</h3>
                        <small class="text-muted">Weekly Progress</small>
                    </div>
                </div>
            </div>
        </div>

        @if(count($nextBadges) > 0)
            <hr class="my-4">
            <h6 class="mb-3">Next Achievements</h6>
            <div class="row g-3">
                @foreach($nextBadges as $badge)
                    <div class="col-md-6">
                        <div class="next-badge p-3 rounded bg-light">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-{{ $badge['icon'] ?? 'award' }} text-primary me-2"></i>
                                <h6 class="mb-0">{{ $badge['name'] }}</h6>
                            </div>
                            <p class="text-muted small mb-2">{{ $badge['description'] }}</p>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" role="progressbar" 
                                    style="width: {{ ($badge['progress'] / $badge['required']) * 100 }}%">
                                </div>
                            </div>
                            <small class="text-muted">{{ $badge['progress'] }}/{{ $badge['required'] }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.streak-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.next-badge {
    transition: transform 0.2s;
}

.next-badge:hover {
    transform: translateY(-2px);
}
</style>
@endpush