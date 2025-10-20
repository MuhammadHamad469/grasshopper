@extends('layouts.app')

@section('content')
    <style>
        /* Base Styles */
        .user-user-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            background: #ffffff;
            transition: transform 0.3s ease;
        }

        .user-user-card:hover {
            transform: translateY(-5px);
        }

        .user-user-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 15px 15px 0 0;
        }

        .user-user-card-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .user-user-card-body {
            padding: 2rem;
            background: #f8f9fc;
        }

        /* Form Elements */
        .user-form-group {
            margin-bottom: 1.5rem;
        }

        .user-form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .user-form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background: white;
        }

        .user-form-input:focus {
            border-color: #667eea;
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .user-form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Select Buttons */
        .user-select-actions {
            margin-bottom: 0.5rem;
        }

        .user-select-btn {
            background: #667eea;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
            font-size: 0.75rem;
            margin-right: 0.5rem;
        }

        .user-select-btn:hover {
            background: #764ba2;
        }

        /* File Upload */
        .user-file-upload {
            position: relative;
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            background: white;
            transition: border-color 0.3s ease;
        }

        .user-file-upload:hover {
            border-color: #667eea;
        }

        .user-file-upload input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            left: 0;
            top: 0;
        }

        /* Submit Button */
        .user-submit-btn {
            background: #667eea;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-submit-btn:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        /* Error States */
        .user-form-input.is-invalid {
            border-color: #fc8181;
        }

        .user-invalid-feedback {
            color: #fc8181;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        /* Select2 Customization */
        .user-select2 .select2-container--default .select2-selection--multiple {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            min-height: 42px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .user-user-card-header {
                padding: 1rem;
            }

            .user-user-card-body {
                padding: 1.5rem;
            }

            .user-form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="user-card">
        <div class="user-card-header">
            Edit User
        </div>

        <div class="user-card-body">

            <form method="POST" action="{{ route('admin.users.update', $id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" value="{{$id}}" name="user_id">
                <div class="user-card mb-4">
                    <div class="user-card-header bg-light">
                        <h5>User Account Information</h5>
                    </div>
                    <div class="user-card-body">
                        <div class="form-group">
                            <label class="required" for="name">Full Name</label>
                            <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text"
                                name="name" id="name" value="{{ old('name', $user->name) }}" required>
                            @if ($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="required" for="email">Email Address</label>
                            <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email"
                                name="email" id="email" value="{{ old('email', $user->email) }}" required>
                            @if ($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password"
                                name="password" id="password" >
                            @if ($errors->has('password'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="role_id">User Roles</label>
                            <select class="form-control select2 {{ $errors->has('roles') ? 'is-invalid' : '' }}"
                                name="role_id" id="roles">
                                @foreach ($roles as $id => $role)
                                    <option value="{{ $id }}"
                                        {{ old('role_id', $user->role_id) == $id ? 'selected' : '' }}>
                                        {{ $role }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('roles'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('roles') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="teams">Assigned Teams</label>
                            <select class="form-control select2 {{ $errors->has('teams') ? 'is-invalid' : '' }}"
                                name="team_id[]" id="teams" multiple>
                                @foreach ($teams as $id => $team)
                                    <option value="{{ $id }}"
                                        {{ in_array($id, old('team_id', $user->teams->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $team }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('teams'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('teams') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="permissions">User Permissions</label>
                            <select class="form-control select2 {{ $errors->has('permissions') ? 'is-invalid' : '' }}"
                                name="permissions[]" id="permissions" multiple>
                                @foreach ($permissions as $id => $permission)
                                    <option value="{{ $id }}"
                                        {{ in_array($id, old('permissions', $user->permissions->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $permission }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('permissions'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('permissions') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="user-card mb-4">
                    <div class="user-card-header bg-light">
                        <h5>Employee Personal Information</h5>
                    </div>
                    <div class="user-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input class="form-control" type="date" name="date_of_birth" id="date_of_birth"
                                        value="{{ old('date_of_birth', \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number">Contact Phone Number</label>
                                    <input class="form-control" type="text" name="phone_number" id="phone_number"
                                        value="{{ old('phone_number', $user->phone_number) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
    <label for="picture">Employee Profile Picture</label>
    <input 
        class="form-control-file" 
        type="file" 
        name="picture" 
        id="picture" 
        accept="image/*">

    @if (!empty($employee->picture_path))
        <div class="mt-2">
            <img src="{{ asset('storage/' . $employee->picture_path) }}" 
                 width="100" 
                 class="img-thumbnail" 
                 alt="Employee Profile Picture">
        </div>
    @endif
</div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_name">Emergency Contact Name</label>
                                    <input class="form-control" type="text" name="emergency_contact_name"
                                        id="emergency_contact_name"
                                        value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emergency_contact_phone">Emergency Contact Phone</label>
                                    <input class="form-control" type="text" name="emergency_contact_phone"
                                        id="emergency_contact_phone"
                                        value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Work Details Section -->
                <div class="user-card mb-4">
                    <div class="user-card-header bg-light">
                        <h5>Work Details</h5>
                    </div>
                    <div class="user-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="employee_type">Employee Type</label>
                                    <select class="form-control" name="employee_type" id="employee_type">
                                        <option value="regular"
                                            {{ old('employee_type', $user->employee_type) == 'regular' ? 'selected' : '' }}>
                                            Regular</option>
                                        <option value="contractor"
                                            {{ old('employee_type', $user->employee_type) == 'contractor' ? 'selected' : '' }}>
                                            Contractor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position">Job Position</label>
                                    <input class="form-control" type="text" name="position" id="position"
                                        value="{{ old('position', $user->position) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Employment Start Date</label>
                                    <input class="form-control" 
       type="date" 
       name="start_date" 
       id="start_date"
       value="{{ old('start_date', isset($employee->start_date) ? \Carbon\Carbon::parse($employee->start_date)->format('Y-m-d') : '') }}">

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Employment End Date</label>
                                    <input class="form-control" 
       type="date" 
       name="end_date" 
       id="end_date"
       value="{{ old('end_date', isset($employee->end_date) ? \Carbon\Carbon::parse($employee->end_date)->format('Y-m-d') : '') }}">

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Leave and Attendance Section -->
                <div class="user-card mb-4">
                    <div class="user-card-header bg-light">
                        <h5>Leave and Attendance</h5>
                    </div>
                    <div class="user-card-body">
                        <div class="form-group">
                            <label for="leave_days_allowed">Annual Leave Days</label>
                            <input class="form-control" type="number" name="leave_days_allowed" id="leave_days_allowed"
                                value="{{ old('leave_days_allowed', $user->leave_days_allowed) }}">
                        </div>
                    </div>
                </div>

                <!-- Financial Information Section -->
                <div class="user-card mb-4">
                    <div class="user-card-header bg-light">
                        <h5>Financial Information</h5>
                    </div>
                    <div class="user-card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="daily_rate">Daily Rate (R)</label>
                                    <input class="form-control" type="number" step="0.01" name="daily_rate"
                                        id="daily_rate" value="{{ old('daily_rate', $user->daily_rate) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="overtime_rate">Overtime Rate (R)</label>
                                    <input class="form-control" type="number" step="0.01" name="overtime_rate"
                                        id="overtime_rate" value="{{ old('overtime_rate', $user->overtime_rate) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tax_number">Tax Number</label>
                                    <input class="form-control" type="text" name="tax_number" id="tax_number"
                                        value="{{ old('tax_number', $user->tax_number) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_name">Bank Name</label>
                                    <input class="form-control" type="text" name="bank_name" id="bank_name"
                                        value="{{ old('bank_name', $user->bank_name) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_account_number">Bank Account Number</label>
                                    <input class="form-control" type="text" name="bank_account_number"
                                        id="bank_account_number"
                                        value="{{ old('bank_account_number', $user->bank_account_number) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="user-card mb-4">
                    <div class="user-card-header bg-light">
                        <h5>Biography</h5>
                    </div>
                    <div class="user-card-body">
                        <div class="form-group">
                            <label for="bio">Bio:</label>
                            <textarea class="form-control" id="bio" name="bio" maxlength="100" rows="4">{{ isset($employee->bio) ? $employee->bio : ""}}</textarea>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <a href="{{ url('/admin/users') }}" class="btn btn-default">
                        <i class="fa fa-arrows-left"></i>
                        Back to Users
                    </a>
                    <button class="btn btn-danger" type="submit">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('.select-all').click(function() {
                let $select2 = $(this).parent().siblings('.select2');
                $select2.find('option').prop('selected', 'selected');
                $select2.trigger('change');
            });

            $('.deselect-all').click(function() {
                let $select2 = $(this).parent().siblings('.select2');
                $select2.find('option').prop('selected', '');
                $select2.trigger('change');
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const bioTextarea = document.getElementById('bio');
            const maxLength = bioTextarea.getAttribute('maxlength');

            // Create character counter element
            const counterDiv = document.createElement('div');
            counterDiv.className = 'text-muted small mt-1';
            counterDiv.id = 'bio-counter';

            // Insert counter after textarea
            bioTextarea.parentNode.insertBefore(counterDiv, bioTextarea.nextSibling);

            // Update counter function
            function updateCounter() {
                const remaining = maxLength - bioTextarea.value.length;
                counterDiv.textContent = `${remaining} characters remaining`;
            }

            // Initialize counter and add event listener
            updateCounter();
            bioTextarea.addEventListener('input', updateCounter);
        });
    </script>
@endsection
