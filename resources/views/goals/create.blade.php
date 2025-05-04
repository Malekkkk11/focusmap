@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create New Goal</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('goals.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category" required>
                                <option value="Personal">Personal</option>
                                <option value="Professional">Professional</option>
                                <option value="Education">Education</option>
                                <option value="Health">Health</option>
                                <option value="Travel">Travel</option>
                                <option value="Financial">Financial</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deadline (optional)</label>
                            <input type="date" class="form-control" name="deadline">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location (optional)</label>
                            <div id="location-map" style="height: 300px; margin-bottom: 10px;"></div>
                            <input type="text" class="form-control mb-2" id="location_name" name="location_name" placeholder="Location name">
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_public" id="is_public">
                                <label class="form-check-label" for="is_public">
                                    Make this goal public
                                </label>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Create Goal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('location-map').setView([0, 0], 2);
    let marker;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng).addTo(map);
        }

        // Reverse geocoding to get location name
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                const locationName = data.display_name;
                document.getElementById('location_name').value = locationName;
            })
            .catch(error => console.error('Error:', error));
    });
});
</script>
@endpush
@endsection