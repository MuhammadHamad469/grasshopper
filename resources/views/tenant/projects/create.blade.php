@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="notify">
                                <form method="POST" action="{{ route('tenant.projects.store') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <!-- Generic Fields -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="project_name">Project Name</label>
                                                <input type="text" class="form-control form-control-sm input"
                                                    placeholder="Project Name" id="project_name" name="project_name"
                                                    value="{{ old('project_name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="project_type_id">Project Type</label>
                                                <select name="project_type_id" id="project_type" class="form-control"
                                                    required>
                                                    <option value="">Select Project Type</option>
                                                    @foreach ($projectTypes as $projectType)
                                                        <option value="{{ $projectType->id }}"
                                                            {{ old('project_type_id') == $projectType->id ? 'selected' : '' }}>
                                                            {{ $projectType->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="startDate">Start Date</label>
                                                <input type="text" class="form-control form-control-sm input start_date"
                                                    id="startDate" placeholder="Project Start Date" name="startDate"
                                                    value="{{ old('startDate') }}" required autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="endDate">End Date</label>
                                                <input type="text" class="form-control form-control-sm input end_date"
                                                    id="endDate" placeholder="Project End Date" name="endDate" required
                                                    value="{{ old('endDate') }}" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea class="form-control" placeholder="Milestone Description" name="description" required rows="3">{{ old('description') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="location_id">Site/Location</label>
                                                <select name="location_id" id="location_id" class="form-control" required>
                                                    <option value="">Select Location</option>
                                                    @foreach ($locations as $location)
                                                        <option value="{{ $location->id }}"
                                                            {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                            {{ $location->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="budget">Budget (R)</label>
                                                <input type="number" class="form-control form-control-sm input"
                                                    placeholder="Budget" id="budget" name="budget" step="0.01"
                                                    value="{{ old('budget') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Status</label>
                                            <div class="form-group">
                                                <select class="form-control form-control-sm input" required name="status">
                                                    <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>
                                                        Planned</option>
                                                    <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>
                                                        Ongoing</option>
                                                    <option value="3" {{ old('status') == 3 ? 'selected' : '' }}>
                                                        Complete</option>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quote_id">Quote</label>
                                                <select name="quote_id" id="quote_id" class="form-control">
                                                    <option value="">Select Quote</option>
                                                    @foreach ($quotes as $quote)
                                                        <option value="{{ $quote->id }}"
                                                            data-url="{{ route('tenant.quotes.show', $quote->id) }}"
                                                            {{ old('quote_id') == $quote->id ? 'selected' : '' }}>
                                                            {{ $quote->quote_number }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                         <div class="col-md-6">
    <div class="form-group">
        <label for="team_leader_user_id">Team Leader</label>
        <select name="team_leader_user_id" required id="team_leader_user_id" class="form-control">
            <option value="">Select Team Leader</option>
            @foreach ($teamLeaders as $teamLeader)
                <option value="{{ $teamLeader->id }}"
                    {{ old('team_leader_user_id') == $teamLeader->id ? 'selected' : '' }}>
                    {{ $teamLeader->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="assets">Assets</label>
                                                <select name="assets[]" id="assets"
                                                    class="form-control select2-multiple" multiple="multiple">
                                                    @foreach ($getAssets as $asset)
                                                        <option value="{{ $asset->id }}"
                                                            {{ in_array($asset->id, old('assets', [])) ? 'selected' : '' }}>
                                                            {{ $asset->name }} ({{ $asset->serial_number }})
                                                        </option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                        <div class="form-group" id="uploadDocumentsSection">
                                            <label for="project_documents">Upload Documents</label>
                                            <input type="file" name="project_documents[]" id="project_documents"
                                                class="form-control" multiple>
                                            <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX. Max
                                                file size: 2MB.</small>
                                        </div>
                                    </div>


                                    <!-- Vegetation Management Fields -->
                                    <div id="vegetationManagementFields" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Target Hectares</label>
                                                    <input type="number" class="form-control form-control-sm input"
                                                        placeholder="Target Hectares" id="target_hectares"
                                                        name="target_hectares" step="0.01" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Actual Hectares Completed</label>
                                                    <input type="number" placeholder="Actual Hectares Completed"
                                                        class="form-control form-control-sm input" id="actual_hectares"
                                                        name="actual_hectares" step="0.01"
                                                        value="{{ old('actual_hectares') }}" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Planned Person days</label>
                                                    <input type="text" placeholder="Planned Person days"
                                                        class="form-control form-control-sm input" id="planned_days"
                                                        name="planned_days" value="{{ old('planned_days') }}" readonly
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Target Hectares Per Day</label>
                                                    <input type="number" class="form-control form-control-sm input"
                                                        placeholder="Target hectares per day" id="hectares_per_day"
                                                        name="hectares_per_day" value="{{ old('hectares_per_day') }}"
                                                        readonly required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Actual Budget</label>
                                                    <input type="number" class="form-control form-control-sm input"
                                                        placeholder="Actual Budget" name="actual_budget" step="0.01"
                                                        required>
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-6">
                                                                                                                                <label>SMME</label>
                                                                                                                                <div class="form-group">
                                                                                                                                    <select name="smme_id" id="smme_id" class="form-control">
                                                                                                                                        <option value="">Select SMME</option>
                                                                                                                                        @foreach ($smmes as $smme)
    <option value="{{ $smme->id }}">{{ $smme->name }}</option>
    @endforeach
                                                                                                                                    </select>
                                                                                                                                </div>
                                                                                                                            </div> -->
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Vehicle KMS Target</label>
                                                    <input type="text" placeholder="Vehicle KMS Target"
                                                        class="form-control form-control-sm input"
                                                        name="vehicle_kms_target" value="{{ old('vehicle_kms_target') }}"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Actual Vehicle KMS</label>
                                                    <input type="text" placeholder="Actual Vehicle KM"
                                                        class="form-control form-control-sm input"
                                                        name="actual_vehicle_kms" value="{{ old('actual_vehicle_kms') }}"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Vegetation Project File Uploads -->
                                        {{-- <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="inspection">Inspection Report</label>
                                                    <input type="file" name="inspection_check" id="inspection_check"
                                                        class="form-control">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>

                                                <div class="form-group">
                                                    <label for="labour_report">Labour Report</label>
                                                    <input type="file" name="labour_report_check"
                                                        id="labour_report_check" class="form-control">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>

                                                <div class="form-group">
                                                    <label for="safety_talk">Safety Talk Document</label>
                                                    <input type="file" name="safety_talk_check" id="safety_talk_check"
                                                        class="form-control">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="herbicide_usage">Herbicide Usage Report</label>
                                                    <input type="file" name="herbicide_check" id="herbicide_check"
                                                        class="form-control">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>

                                                <div class="form-group">
                                                    <label for="invoice">Invoice</label>
                                                    <input type="file" name="invoice_check" id="invoice_check"
                                                        class="form-control">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="inspection">Inspection Report</label>
                                                    <input type="file" name="inspection_check" id="inspection_check"
                                                        class="form-control" tabindex="1">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>

                                                <div class="form-group">
                                                    <label for="labour_report">Labour Report</label>
                                                    <input type="file" name="labour_report_check"
                                                        id="labour_report_check" class="form-control" tabindex="3">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>

                                                <div class="form-group">
                                                    <label for="safety_talk">Safety Talk Document</label>
                                                    <input type="file" name="safety_talk_check" id="safety_talk_check"
                                                        class="form-control" tabindex="5">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="herbicide_usage">Herbicide Usage Report</label>
                                                    <input type="file" name="herbicide_check" id="herbicide_check"
                                                        class="form-control" tabindex="2">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>

                                                <div class="form-group">
                                                    <label for="invoice">Invoice</label>
                                                    <input type="file" name="invoice_check" id="invoice_check"
                                                        class="form-control" tabindex="4">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Training Fields -->
                                    <div id="trainingFields" style="display: none;">
                                        <div class="row">
                                            <!-- Left Column -->
                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <label>Name of facilitator</label>
                                                    <input type="text" class="form-control form-control-sm input"
                                                        placeholder="Name of facilitator" id="facilitator_name"
                                                        name="facilitator_name" value="{{ old('facilitator_name') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="facilitation">Experience CV</label>
                                                    <input type="file" name="experience_cv" id="experience_cv"
                                                        class="form-control">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="assessment">Assessment Document</label>
                                                    <input type="file" name="assessment_check" id="assessment_check"
                                                        class="form-control">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="facilitation">Facilitation Document</label>
                                                    <input type="file" name="facilitation_check"
                                                        id="facilitation_check" class="form-control">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>
                                            </div>

                                            <!-- Right Column -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Number Of Students</label>
                                                    <input type="text" class="form-control form-control-sm input"
                                                        placeholder="Number Of Students" id="number_of_students"
                                                        name="number_of_students" value="{{ old('number_of_students') }}"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="moderation">Moderation Document</label>
                                                    <input type="file" name="moderation_check" id="moderation_check"
                                                        class="form-control">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="database_administration">Database Administration
                                                        Document</label>
                                                    <input type="file" name="database_admin_check"
                                                        id="database_admin_check" class="form-control">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="certification">Certification Document</label>
                                                    <input type="file" name="certification_check"
                                                        id="certification_check" class="form-control">
                                                    <small class="form-text text-muted">Allowed file types: PDF, DOC, DOCX.
                                                        Max file size: 2MB.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ url()->previous() }}" class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i>
                                            Back to Project</a>
                                        <button type="submit" name="createProject"
                                            class="btn btn-md waves-effect waves-light"
                                            style="background: #086177;border:1px solid #a39d9d;color:white;">Create
                                            Project
                                        </button>
                                        <div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery and jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script>
        $(function() {
            // Initialize datepickers
            $(".start_date, .end_date").datepicker({
                dateFormat: 'yy-mm-dd'
            });

            function toggleRequiredFields() {
                var projectType = $('#project_type').val();

                // Remove required from all potentially hidden fields
                $('#vegetationManagementFields input, #vegetationManagementFields select, #vegetationManagementFields textarea')
                    .prop('required', false);
                $('#trainingFields input, #trainingFields select, #trainingFields textarea').prop('required',
                    false);

                if (projectType === '1') { // Vegetation Management
                    $('#vegetationManagementFields').show();
                    $('#trainingFields').hide();
                    $('#vegetationManagementFields input:not([type="checkbox"]), #vegetationManagementFields select, #vegetationManagementFields textarea')
                        .prop('required', false);
                    $('#uploadDocumentsSection').hide(); // Hide Upload Documents
                } else if (projectType === '2') { // Training
                    $('#trainingFields').show();
                    $('#vegetationManagementFields').hide();
                    $('#trainingFields input:not([type="checkbox"]), #trainingFields select, #trainingFields textarea')
                        .prop('required', false);
                    $('#uploadDocumentsSection').hide(); // Hide Upload Documents
                } else { // Other project types
                    $('#vegetationManagementFields, #trainingFields').hide();
                    $('#uploadDocumentsSection').show(); // Show Upload Documents
                }
                $('input[type="checkbox"]').prop('required', false);
            }

            function isWeekend(date) {
                const day = date.getDay();
                return day === 0 || day === 6; // 0 is Sunday, 6 is Saturday
            }

            function getEasterDate(year) {
                const a = year % 19;
                const b = Math.floor(year / 100);
                const c = year % 100;
                const d = Math.floor(b / 4);
                const e = b % 4;
                const f = Math.floor((b + 8) / 25);
                const g = Math.floor((b - f + 1) / 3);
                const h = (19 * a + b - d - g + 15) % 30;
                const i = Math.floor(c / 4);
                const k = c % 4;
                const l = (32 + 2 * e + 2 * i - h - k) % 7;
                const m = Math.floor((a + 11 * h + 22 * l) / 451);
                const month = Math.floor((h + l - 7 * m + 114) / 31) - 1;
                const day = ((h + l - 7 * m + 114) % 31) + 1;
                return new Date(year, month, day);
            }

            function isHoliday(date) {
                const year = date.getFullYear();
                const easter = getEasterDate(year);
                const goodFriday = new Date(easter);
                goodFriday.setDate(easter.getDate() - 2);
                const easterMonday = new Date(easter);
                easterMonday.setDate(easter.getDate() + 1);

                const holidays = [
                    new Date(year, 0, 1), // New Year's Day
                    new Date(year, 2, 21), // Human Rights Day
                    goodFriday, // Good Friday (calculated)
                    easterMonday, // Family Day (calculated)
                    new Date(year, 3, 27), // Freedom Day
                    new Date(year, 4, 1), // Workers' Day
                    new Date(year, 5, 16), // Youth Day
                    new Date(year, 7, 9), // National Women's Day
                    new Date(year, 8, 24), // Heritage Day
                    new Date(year, 11, 16), // Day of Reconciliation
                    new Date(year, 11, 25), // Christmas Day
                    new Date(year, 11, 26) // Day of Goodwill
                ];

                // Check if the date matches any holiday
                return holidays.some(holiday =>
                    holiday.getDate() === date.getDate() &&
                    holiday.getMonth() === date.getMonth() &&
                    holiday.getFullYear() === date.getFullYear()
                );
            }

            function calculatePlannedDays() {
                var startDate = $(".start_date").datepicker("getDate");
                var endDate = $(".end_date").datepicker("getDate");

                if (startDate && endDate) {
                    let workingDays = 0;
                    const currentDate = new Date(startDate);

                    while (currentDate <= endDate) {
                        if (!isWeekend(currentDate) && !isHoliday(currentDate)) {
                            workingDays++;
                        }
                        currentDate.setDate(currentDate.getDate() + 1);
                    }

                    $("#planned_days").val(workingDays);
                } else {
                    $("#planned_days").val('');
                }
                calculateHectaresPerDay();
            }

            function calculateHectaresPerDay() {
                var targetHectares = parseFloat($("#target_hectares").val());
                var plannedDays = parseFloat($("#planned_days").val());

                if (!isNaN(targetHectares) && !isNaN(plannedDays) && plannedDays > 0) {
                    var hectaresPerDay = targetHectares / plannedDays;
                    $("#hectares_per_day").val(hectaresPerDay.toFixed(2));
                } else {
                    $("#hectares_per_day").val('');
                }
            }

            function updateQuoteLink() {
                const selectedOption = $('#quote_id option:selected');
                const isSelected = selectedOption.val() !== "";
                $('#quote_check').prop('checked', isSelected);

                if (isSelected) {
                    const url = selectedOption.data('url');
                    $('#view_quote_link').attr('href', url).show();
                } else {
                    $('#view_quote_link').hide();
                }
            }

            // Event listeners
            toggleRequiredFields();
            $('#project_type').change(toggleRequiredFields);

            $(".start_date, .end_date").on('change', function() {
                calculatePlannedDays();
            });

            $("#target_hectares").on('input', function() {
                calculateHectaresPerDay();
            });

            $('#quote_id').change(updateQuoteLink);

            $('#quote_check').change(function() {
                if ($(this).is(':checked')) {
                    updateQuoteLink();
                } else {
                    $('#view_quote_link').hide();
                }
            });

            const trainingCheckboxes = $('#trainingFields input[type="checkbox"]');
            trainingCheckboxes.on('change', function() {
                const totalCheckboxes = trainingCheckboxes.length;
                const checkedCount = trainingCheckboxes.filter(':checked').length;
                const progressPercent = Math.round((checkedCount / totalCheckboxes) * 100);

                $('#training-progress')
                    .css('width', progressPercent + '%')
                    .attr('aria-valuenow', progressPercent)
                    .text(progressPercent + '%');
            });

            $('form').on('submit', function(e) {
                console.log('Form submitted');
            });

            $('#assets').select2({
                placeholder: "Select assets",
                allowClear: true,
                width: '100%'
            });

            // Initial calculations and updates
            calculatePlannedDays();
            calculateHectaresPerDay();
            updateQuoteLink();
        });
    </script>
@endsection
