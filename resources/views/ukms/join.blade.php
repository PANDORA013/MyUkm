@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Gabung UKM</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('ukms.join') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="kode_ukm">Kode UKM</label>
            <input type="text" name="kode_ukm" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Gabung</button>
    </form>
</div>
@endsection
