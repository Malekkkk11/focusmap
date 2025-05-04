@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Goal</h2>

    <form action="{{ route('goals.update', $goal->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $goal->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $goal->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category" required>
                @foreach(['Personal', 'Professional', 'Education', 'Health', 'Travel', 'Financial', 'Other'] as $category)
                    <option value="{{ $category }}" {{ $goal->category === $category ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="deadline" class="form-label">Deadline</label>
            <input type="date" class="form-control" id="deadline" name="deadline" value="{{ $goal->deadline }}">
        </div>
        <div class="mb-3">
    <label for="progress" class="form-label">Progress (%)</label>
    <input type="number" class="form-control" id="progress" name="progress" min="0" max="100" value="{{ old('progress', $goal->progress) }}">
</div>


        <div class="mb-3 form-check">
            <input class="form-check-input" type="checkbox" id="is_public" name="is_public" {{ $goal->is_public ? 'checked' : '' }}>
            <label class="form-check-label" for="is_public">
                Make this goal public
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Update Goal</button>
        <a href="{{ route('goals.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
