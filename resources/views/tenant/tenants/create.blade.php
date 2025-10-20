@extends('layouts.app')

@section('content')
    <h2>Create Tenant</h2>

    <form method="POST" action="{{ route('admin.tenants.create') }}">
        @csrf

        <div class="form-group">
            <label for="tenant_name">Tenant Name:</label>
            <input type="text" class="form-control" id="tenant_name" name="tenant_name" required>
        </div>

        <div class="form-group">
            <label for="contact_person">Contact Person:</label>
            <input type="text" class="form-control" id="contact_person" name="contact_person" required>
        </div>

        <div class="form-group">
            <label for="contact_email">Contact Email:</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" required>
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="tel" class="form-control" id="phone_number" name="phone_number">
        </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Tenant</button>
    </form>

@endsection