<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Progress Journal</h5>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addJournalModal">
            Add Entry
        </button>
    </div>
    <div class="card-body">
        @if($goal->journals->count() > 0)
            <div class="timeline">
                @foreach($goal->journals->sortByDesc('created_at') as $journal)
                    <div class="timeline-item">
                        <div class="timeline-marker">
                            @if($journal->mood)
                                <i class="bi bi-emoji-{{ $journal->mood }} text-{{ $journal->mood === 'happy' ? 'success' : ($journal->mood === 'sad' ? 'danger' : 'warning') }}"></i>
                            @else
                                <i class="bi bi-circle-fill text-primary"></i>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">{{ $journal->created_at->format('M d, Y g:i A') }}</small>
                                <div class="dropdown">
                                    <button class="btn btn-link btn-sm p-0" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item" onclick="editJournal({{ $journal->id }})">
                                                Edit
                                            </button>
                                        </li>
                                        <li>
                                            <form action="{{ route('journals.destroy', $journal) }}" method="POST">
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
                            <p class="mb-2">{{ $journal->content }}</p>
                            @if($journal->progress_update !== null)
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" 
                                        style="width: {{ $journal->progress_update }}%">
                                    </div>
                                </div>
                                <small class="text-muted">Progress updated to {{ $journal->progress_update }}%</small>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-muted mb-0">No journal entries yet</p>
            </div>
        @endif
    </div>
</div>

<!-- Add Journal Modal -->
<div class="modal fade" id="addJournalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('journals.store', $goal) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Journal Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="content" class="form-label">Your Thoughts</label>
                        <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">How are you feeling?</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="mood" value="happy" id="mood_happy">
                            <label class="btn btn-outline-success" for="mood_happy">
                                <i class="bi bi-emoji-smile"></i> Happy
                            </label>
                            
                            <input type="radio" class="btn-check" name="mood" value="neutral" id="mood_neutral">
                            <label class="btn btn-outline-warning" for="mood_neutral">
                                <i class="bi bi-emoji-neutral"></i> Neutral
                            </label>
                            
                            <input type="radio" class="btn-check" name="mood" value="sad" id="mood_sad">
                            <label class="btn btn-outline-danger" for="mood_sad">
                                <i class="bi bi-emoji-frown"></i> Sad
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Progress Update (optional)</label>
                        <input type="range" class="form-range" name="progress_update" min="0" max="100" step="5">
                        <div class="text-center">
                            <small class="text-muted">Progress: <span id="progressValue">0</span>%</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Journal Modal -->
<div class="modal fade" id="editJournalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editJournalForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Journal Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Your Thoughts</label>
                        <textarea class="form-control" id="edit_content" name="content" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">How are you feeling?</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="mood" value="happy" id="edit_mood_happy">
                            <label class="btn btn-outline-success" for="edit_mood_happy">
                                <i class="bi bi-emoji-smile"></i> Happy
                            </label>
                            
                            <input type="radio" class="btn-check" name="mood" value="neutral" id="edit_mood_neutral">
                            <label class="btn btn-outline-warning" for="edit_mood_neutral">
                                <i class="bi bi-emoji-neutral"></i> Neutral
                            </label>
                            
                            <input type="radio" class="btn-check" name="mood" value="sad" id="edit_mood_sad">
                            <label class="btn btn-outline-danger" for="edit_mood_sad">
                                <i class="bi bi-emoji-frown"></i> Sad
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Progress Update (optional)</label>
                        <input type="range" class="form-range" name="progress_update" id="edit_progress_update" min="0" max="100" step="5">
                        <div class="text-center">
                            <small class="text-muted">Progress: <span id="editProgressValue">0</span>%</small>
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

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 0;
    list-style: none;
}

.timeline-item {
    position: relative;
    padding-left: 2rem;
    padding-bottom: 2rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 50%;
}

.timeline-marker::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 24px;
    bottom: -24px;
    width: 2px;
    margin-left: -1px;
    background: var(--bs-primary);
    opacity: 0.2;
}

.timeline-item:last-child .timeline-marker::before {
    display: none;
}

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-left: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle progress range input for Add Journal Modal
    const progressRange = document.querySelector('input[name="progress_update"]');
    const progressValue = document.getElementById('progressValue');
    if (progressRange) {
        progressRange.addEventListener('input', function() {
            progressValue.textContent = this.value;
        });
    }

    // Handle progress range input for Edit Journal Modal
    const editProgressRange = document.getElementById('edit_progress_update');
    const editProgressValue = document.getElementById('editProgressValue');
    if (editProgressRange) {
        editProgressRange.addEventListener('input', function() {
            editProgressValue.textContent = this.value;
        });
    }
});

function editJournal(journalId) {
    fetch(`/journals/${journalId}`)
        .then(response => response.json())
        .then(journal => {
            document.getElementById('edit_content').value = journal.content;
            if (journal.mood) {
                document.getElementById(`edit_mood_${journal.mood}`).checked = true;
            }
            if (journal.progress_update !== null) {
                document.getElementById('edit_progress_update').value = journal.progress_update;
                document.getElementById('editProgressValue').textContent = journal.progress_update;
            }
            document.getElementById('editJournalForm').action = `/journals/${journal.id}`;
            new bootstrap.Modal(document.getElementById('editJournalModal')).show();
        });
}
</script>
@endpush