@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">Send custom notification to user</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.bookings.notify.send', $booking->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Booking</label>
                            <div>{{ $booking->order_number ?? ('#' . $booking->id) }} — {{ data_get($booking, 'user.name') ?? data_get($booking, 'user.phone') }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Title (optional)</label>
                            <input id="title" name="title" class="form-control" maxlength="255" value="{{ old('title') }}">
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea id="message" name="message" class="form-control" rows="6" required>{{ old('message') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-secondary">Cancel</a>
                            <button class="btn btn-primary">Send notification</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
