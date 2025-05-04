@extends('layouts.app')

@section('content')
<style>
    .dashboard-title {
        font-weight: 700;
        font-size: 2rem;
        color: #2c3e50;
        margin-bottom: 2rem;
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        background-color: transparent;
        border-bottom: none;
        font-weight: 600;
        color: #2c3e50;
    }

    .progress {
        height: 10px;
        border-radius: 20px;
    }

    .badge {
        font-size: 0.85rem;
    }

    .list-group-item {
        border: none;
        border-bottom: 1px solid #f0f0f0;
    }

    .list-group-item:hover {
        background-color: #f9f9f9;
    }

    .text-muted {
        font-size: 0.9rem;
    }
</style>

<div class="container py-4">
    <h1 class="dashboard-title">🎯 Tableau de bord</h1>

    <div class="row mb-4">
        <!-- Statistiques rapides -->
        <div class="col-md-3">
            <div class="card bg-primary text-white text-center">
                <div class="card-body">
                    <h5 class="card-title">Objectifs actifs</h5>
                    <h2>{{ $activeGoals }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white text-center">
                <div class="card-body">
                    <h5 class="card-title">Étapes terminées</h5>
                    <h2>{{ $completedSteps }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white text-center">
                <div class="card-body">
                    <h5 class="card-title">Objectifs géolocalisés</h5>
                    <h2>{{ $goalsWithLocation }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white text-center">
                <div class="card-body">
                    <h5 class="card-title">Objectifs à échéance</h5>
                    <h2>{{ $goalsDueSoon }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Avancement récent -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Progression récente</div>
                <div class="card-body">
                    @forelse($recentGoals as $goal)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0">{{ $goal->title }}</h6>
                                <span class="badge bg-{{ $goal->progress == 100 ? 'success' : 'primary' }}">
                                    {{ number_format($goal->progress) }}%
                                </span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ $goal->progress }}%"
                                     aria-valuenow="{{ $goal->progress }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">Aucun objectif récent</p>
                    @endforelse
                </div>
            </div>

            <!-- Répartition par catégories -->
            <div class="card">
                <div class="card-header">Répartition des objectifs</div>
                <div class="card-body">
                    @foreach($goalsByCategory as $category => $count)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $category }}</span>
                                <span class="text-muted">{{ $count }} objectif(s)</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" role="progressbar"
                                     style="width: {{ ($count / max($totalGoals, 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Échéances à venir -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Échéances proches</div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingDeadlines as $goal)
                            <a href="{{ route('goals.show', $goal) }}" class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">{{ $goal->title }}</h6>
                                    <span class="badge bg-{{ $goal->deadline->isPast() ? 'danger' : 'warning' }}">
                                        {{ $goal->deadline->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $goal->progress }}%">
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-muted">Aucune échéance à venir</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
