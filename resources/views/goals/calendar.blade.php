@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Goal Calendar</h5>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Upcoming Deadlines</h5>
                </div>
                <div class="card-body">
                    @foreach($upcomingGoals as $goal)
                        <div class="deadline-item mb-3">
                            <h6 class="mb-1">{{ $goal->title }}</h6>
                            <p class="text-muted small mb-1">Due: {{ $goal->deadline->format('M d, Y') }}</p>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar" 
                                    style="width: {{ $goal->progress }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.css' rel='stylesheet' />
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: @json($calendarEvents),
        eventClick: function(info) {
            window.location.href = `/goals/${info.event.id}`;
        }
    });
    calendar.render();
});
</script>
@endpush
@endsection