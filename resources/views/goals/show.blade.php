@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">{{ $goal->title }}</h2>
                <div class="dropdown">
                    <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editGoalModal">
                                <i class="bi bi-pencil me-2"></i> Edit Goal
                            </button>
                        </li>
                        <li>
                            <form action="{{ route('goals.destroy', $goal) }}" method="POST" 
                                onsubmit="return confirm('Are you sure you want to delete this goal?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-trash me-2"></i> Delete Goal
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="progress mb-3" style="height: 20px;">
                <div class="progress-bar" role="progressbar" 
                    style="width: {{ $goal->progress }}%"
                    aria-valuenow="{{ $goal->progress }}" 
                    aria-valuemin="0" 
                    aria-valuemax="100">
                    {{ number_format($goal->progress, 0) }}%
                </div>
            </div>

            <p class="text-muted">{{ $goal->description }}</p>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-primary">{{ $goal->category }}</span>
                @if($goal->deadline)
                    <span class="badge bg-info">
                        <i class="bi bi-calendar me-1"></i>
                        Due {{ $goal->deadline->format('M d, Y') }}
                    </span>
                @endif
                @if($goal->location_name)
                    <span class="badge bg-secondary">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $goal->location_name }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#steps-tab" type="button">
                        Steps
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#journal-tab" type="button">
                        Journal
                    </button>
                </li>
                @if($goal->latitude && $goal->longitude)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#map-tab" type="button">
                            Map
                        </button>
                    </li>
                @endif
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="steps-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Steps</h4>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStepModal">
                            <i class="bi bi-plus-lg"></i> Add Step
                        </button>
                    </div>

                    <div class="steps-list">
                        @forelse($goal->steps->sortBy('order') as $step)
                            <div class="card mb-3 step-item" draggable="true" 
                                data-step-id="{{ $step->id }}" 
                                data-order="{{ $step->order }}">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" 
                                                {{ $step->completed ? 'checked' : '' }}
                                                onchange="updateStep({{ $step->id }}, this.checked)">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="mb-1">{{ $step->title }}</h5>
                                                <div class="dropdown">
                                                    <button class="btn btn-link btn-sm p-0" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <button class="dropdown-item" onclick="editStep({{ $step->id }})">
                                                                Edit
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('steps.destroy', $step) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            @if($step->description)
                                                <p class="mb-1">{{ $step->description }}</p>
                                            @endif
                                            @if($step->due_date)
                                                <small class="text-muted">
                                                    Due: {{ $step->due_date->format('M d, Y') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">No steps added yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="tab-pane fade" id="journal-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Journal Entries</h4>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addJournalModal">
                            <i class="bi bi-plus-lg"></i> Add Entry
                        </button>
                    </div>

                    <x-journal-timeline :goal="$goal" />
                </div>

                @if($goal->latitude && $goal->longitude)
                    <div class="tab-pane fade" id="map-tab">
                        <div id="map" style="height: 400px;"></div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($goal->journals()->latest()->take(5)->get() as $journal)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <p class="mb-1">{{ Str::limit($journal->content, 100) }}</p>
                                    <small class="text-muted">{{ $journal->created_at->diffForHumans() }}</small>
                                </div>
                                @if($journal->progress_update)
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" 
                                            style="width: {{ $journal->progress_update }}%">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bouton d'ouverture de la modale -->
<!-- remplace le bouton existant -->
<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
    <i class="bi bi-plus-lg"></i> Ajouter une étape
</button>

<!-- Nouvelle Modale propre -->
<div class="modal fade" id="exampleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('steps.store', ['goal' => $goal->id]) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Nouvelle Étape</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" class="form-control" name="title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Date limite</label>
            <input type="date" class="form-control" name="due_date">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Ajouter</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Edit Goal Modal -->
<div class="modal fade" id="editGoalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('goals.update', $goal) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Goal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" 
                               value="{{ $goal->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ $goal->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" required>
                            <option value="Personal" {{ $goal->category == 'Personal' ? 'selected' : '' }}>Personal</option>
                            <option value="Professional" {{ $goal->category == 'Professional' ? 'selected' : '' }}>Professional</option>
                            <option value="Education" {{ $goal->category == 'Education' ? 'selected' : '' }}>Education</option>
                            <option value="Health" {{ $goal->category == 'Health' ? 'selected' : '' }}>Health</option>
                            <option value="Travel" {{ $goal->category == 'Travel' ? 'selected' : '' }}>Travel</option>
                            <option value="Financial" {{ $goal->category == 'Financial' ? 'selected' : '' }}>Financial</option>
                            <option value="Other" {{ $goal->category == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deadline (optional)</label>
                        <input type="date" class="form-control" name="deadline" 
                               value="{{ $goal->deadline ? $goal->deadline->format('Y-m-d') : '' }}">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_public" 
                                   id="is_public" {{ $goal->is_public ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_public">
                                Make this goal public
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    @if($goal->latitude && $goal->longitude)
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('map').setView([{{ $goal->latitude }}, {{ $goal->longitude }}], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        L.marker([{{ $goal->latitude }}, {{ $goal->longitude }}])
            .addTo(map)
            .bindPopup("{{ $goal->location_name ?: $goal->title }}");
    });
    @endif

    // Update active tab based on URL hash
    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash;
        if (hash) {
            const tab = document.querySelector(`[data-bs-target="${hash}"]`);
            if (tab) {
                new bootstrap.Tab(tab).show();
            }
        }
    });

    // Update URL hash when tab changes
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            const target = e.target.getAttribute('data-bs-target');
            window.location.hash = target;
        });
    });

    // Step completion handling
    function updateStep(stepId, completed) {
        fetch(`/steps/${stepId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ completed })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update progress bar
                const progress = data.progress;
                document.querySelector('.progress-bar').style.width = `${progress}%`;
                document.querySelector('.progress-bar').textContent = `${Math.round(progress)}%`;
            }
        });
    }
</script>
@endpush

@push('styles')
<style>
.step-item.dragging {
    opacity: 0.5;
}

.step-item.drag-over {
    border-top: 2px solid var(--bs-primary);
}

.map-container {
    border-radius: 0.375rem;
    overflow: hidden;
}
</style>
@endpush