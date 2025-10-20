@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Supplier</h1>

        <form action="{{ route('tenant.smmes.update', $smme) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Business Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $smme->name }}" required>
            </div>
            <div class="mb-3">
                <label for="registration_number" class="form-label">Registration Number</label>
                <input type="text" class="form-control" id="registration_number" name="registration_number"
                       value="{{ $smme->registration_number }}" required>
            </div>
            <div class="mb-3">
                <label for="years_of_experience" class="form-label">Years of Experience</label>
                <input type="number" class="form-control" id="years_of_experience" name="years_of_experience"
                       value="{{ $smme->years_of_experience }}" required>
            </div>
            <div class="mb-3">
                <label for="team_composition" class="form-label">Team Composition</label>
                <textarea class="form-control" id="team_composition" name="team_composition"
                          required>{{ $smme->team_composition }}</textarea>
            </div>
            <div class="mb-3">
                <label for="documents_verified" class="form-label">Documents Verified</label>
                <select class="form-control" id="documents_verified" name="documents_verified" required>
                    <option value="true" @if (old('documents_verified', $smme->documents_verified)) selected @endif>
                        Yes
                    </option>
                    <option value="false" @if (!old('documents_verified', $smme->documents_verified)) selected @endif>
                        No
                    </option>
                </select>
            </div>
            <div class="mb-3">
                <label for="grade" class="form-label">Grade</label>
                <select class="form-control" id="grade" name="grade" required>
                    <option value="A" {{ old('grade', $smme->grade) == 'A' ? 'selected' : '' }}>A</option>
                    <option value="B" {{ old('grade', $smme->grade) == 'B' ? 'selected' : '' }}>B</option>
                    <option value="C" {{ old('grade', $smme->grade) == 'C' ? 'selected' : '' }}>C</option>
                    <option value="D" {{ old('grade', $smme->grade) == 'D' ? 'selected' : '' }}>D</option>
                    <option value="E" {{ old('grade', $smme->grade) == 'E' ? 'selected' : '' }}>E</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="green" {{ $smme->status == 'green' ? 'selected' : '' }}>Green</option>
                    <option value="yellow" {{ $smme->status == 'yellow' ? 'selected' : '' }}>Yellow</option>
                    <option value="red" {{ $smme->status == 'red' ? 'selected' : '' }}>Red</option>
                </select>
            </div>
            <br>
                        <!-- File Upload Fields -->
<div class="form-group">
    <label for="company_registration">Company Registration</label>
    <input type="file" class="form-control" name="company_registration" id="company_registration">
</div>

<div class="form-group">
    <label for="tax_certificate">Tax Certificate</label>
    <input type="file" class="form-control" name="tax_certificate" id="tax_certificate">
</div>

<div class="form-group">
    <label for="bee_certificate">BEE Certificate</label>
    <input type="file" class="form-control" name="bee_certificate" id="bee_certificate">
</div>

<div class="form-group">
    <label for="company_profile">Company Profile</label>
    <input type="file" class="form-control" name="company_profile" id="company_profile">
</div>
            <br>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
            @include('partials.save-button')
            @include('partials.icon_button', ['href' => route('tenant.smmes.index'), 'type' => 'danger', 'icon' => 'fa-arrow-left', 'slot' => 'Back'])
        </form>
    </div>
@endsection