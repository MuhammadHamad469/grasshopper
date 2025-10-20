@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Supplier Details</h1>
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-6 readonly-input">
                            <label for="name" class="form-label"><strong>Business Name:</strong></label>
                            <input type="text" id="name" class="form-control readonly-input"
                                   style="background-color: white;" value="{{ $smme->name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="registration_number" class="form-label"><strong>Registration
                                    Number:</strong></label>
                            <input type="text" id="registration_number" class="form-control"
                                   style="background-color: white;" value="{{ $smme->registration_number }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="years_of_experience" class="form-label"><strong>Years Of
                                    Experience:</strong></label>
                            <input type="text" id="years_of_experience" class="form-control"
                                   style="background-color: white;" value="{{ $smme->years_of_experience }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label"><strong>Status:</strong></label>
                            <input type="text" id="status" class="form-control"
                                   style="background-color: white; color:
                                       @if ($smme->status == 'red') red
                                       @elseif ($smme->status == 'yellow') orange
                                       @elseif ($smme->status == 'green') green
                                       @endif;"
                                   value="{{ $smme->status }}" readonly>
                        </div>
                        <div class="col-md-12">
                            <label for="team_composition" class="form-label"><strong>Team Composition:</strong></label>
                            <textarea id="team_composition" class="form-control"
                                      style="background-color: white;" rows="3"
                                      readonly>{{ $smme->team_composition }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="documents_verified" class="form-label"><strong>Documents
                                    Verified:</strong></label>
                            <input type="text" id="documents_verified" class="form-control"
                                   style="background-color: white; color: {{ $smme->documents_verified == 1 ? 'green' : 'red' }};"
                                   value="{{ $smme->documents_verified ? 'Yes' : 'No' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="grade" class="form-label"><strong>Grade:</strong></label>
                            <input type="text" id="grade" class="form-control"
                                   style="background-color: white;" value="{{ $smme->grade }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="created_at" class="form-label"><strong>Created At:</strong></label>
                            <input type="text" id="created_at" class="form-control"
                                   style="background-color: white;" value="{{ $smme->created_at }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="updated_at" class="form-label"><strong>Updated At:</strong></label>
                            <input type="text" id="updated_at" class="form-control"
                                   style="background-color: white;" value="{{ $smme->updated_at }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Company Documents</h5>
                            <ul class="list-group">
                                @if ($smme->company_registration)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <b>Company Registration:</b>
                                        <a href="https://{{ env('AWS_BUCKET') }}.s3.{{ env('AWS_DEFAULT_REGION') }}.amazonaws.com/{{ $smme->company_registration }}" target="_blank">View Document</a>
                                    </li>
                                @endif
                        
                                @if ($smme->tax_certificate)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <b>Tax Certificate:</b>
                                        <a href="https://{{ env('AWS_BUCKET') }}.s3.{{ env('AWS_DEFAULT_REGION') }}.amazonaws.com/{{ $smme->tax_certificate }}" target="_blank">View Document</a>
                                    </li>
                                @endif
                        
                                @if ($smme->bee_certificate)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <b>BEE Certificate:</b>
                                        <a href="https://{{ env('AWS_BUCKET') }}.s3.{{ env('AWS_DEFAULT_REGION') }}.amazonaws.com/{{ $smme->bee_certificate }}" target="_blank">View Document</a>
                                    </li>
                                @endif
                        
                                @if ($smme->company_profile)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <b>Company Profile:</b>
                                        <a href="https://{{ env('AWS_BUCKET') }}.s3.{{ env('AWS_DEFAULT_REGION') }}.amazonaws.com/{{ $smme->company_profile }}" target="_blank">View Document</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>

        <a href="{{ route('tenant.smmes.edit', $smme) }}" class="btn btn-primary mt-3">Edit</a>
        <a href="{{ route('tenant.smmes.index') }}" class="btn btn-secondary mt-3">Back to List</a>
    </div>
@endsection