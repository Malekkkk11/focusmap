@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Profile Settings</h5>
                </div>
                <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" class="mb-4">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select @error('timezone') is-invalid @enderror" 
                                    id="timezone" name="timezone">
                                @foreach(timezone_identifiers_list() as $timezone)
                                    <option value="{{ $timezone }}" 
                                            {{ old('timezone', $user->timezone) == $timezone ? 'selected' : '' }}>
                                        {{ $timezone }}
                                    </option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block">Notification Preferences</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" 
                                       id="email_notifications" name="notification_preferences[]" 
                                       value="email" @checked(in_array('email', $user->notification_preferences ?? []))>
                                <label class="form-check-label" for="email_notifications">
                                    Email Notifications
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" 
                                       id="push_notifications" name="notification_preferences[]" 
                                       value="push" @checked(in_array('push', $user->notification_preferences ?? []))>
                                <label class="form-check-label" for="push_notifications">
                                    Push Notifications
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>
                    </form>

                    <hr class="my-4">

                    <h6 class="mb-3">Profile Picture</h6>
                    <form action="{{ route('profile.avatar.update') }}" method="POST" 
                          enctype="multipart/form-data" class="mb-4">
                        @csrf
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" 
                                 class="rounded-circle me-3" 
                                 width="64" 
                                 height="64" 
                                 alt="{{ $user->name }}">
                            <div>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                       id="avatar" name="avatar" accept="image/*">
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            Update Profile Picture
                        </button>
                    </form>

                    <hr class="my-4">

                    <h6 class="mb-3 text-danger">Danger Zone</h6>
                    <p class="text-muted small">Once you delete your account, there is no going back. Please be certain.</p>
                    <form action="{{ route('profile.destroy') }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            Delete Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection