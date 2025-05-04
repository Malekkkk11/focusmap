@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Public Goals</h5>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            Sort by: {{ request('sort', 'Latest') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('goals.public', ['sort' => 'Latest']) }}">Latest</a></li>
                            <li><a class="dropdown-item" href="{{ route('goals.public', ['sort' => 'Popular']) }}">Popular</a></li>
                            <li><a class="dropdown-item" href="{{ route('goals.public', ['sort' => 'Progress']) }}">Progress</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($goals as $goal)
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="card-title mb-3">{{ $goal->title }}</h5>
                                            <span class="badge bg-{{ $goal->progress == 100 ? 'success' : 'primary' }}">
                                                {{ $goal->progress }}%
                                            </span>
                                        </div>
                                        <p class="card-text text-muted">{{ Str::limit($goal->description, 100) }}</p>
                                        
                                        @if($goal->location_name)
                                            <p class="card-text small">
                                                <i class="bi bi-geo-alt"></i> {{ $goal->location_name }}
                                            </p>
                                        @endif
                                        
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar" role="progressbar" 
                                                style="width: {{ $goal->progress }}%">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                By {{ $goal->user->name }}
                                            </small>
                                            <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-outline-primary">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted text-center my-5">No public goals found</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $goals->links() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Categories</h5>
                </div>
                <div class="card-body">
                    @foreach($categories as $category => $count)
                        <a href="{{ route('goals.public', ['category' => $category]) }}" 
                           class="btn btn-outline-primary btn-sm me-2 mb-2">
                            {{ $category }} ({{ $count }})
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Popular Users</h5>
                </div>
                <div class="card-body">
                    @foreach($popularUsers as $user)
                        <div class="d-flex align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">{{ $user->name }}</h6>
                                <small class="text-muted">{{ $user->public_goals_count }} public goals</small>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-primary">{{ $user->total_progress }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection