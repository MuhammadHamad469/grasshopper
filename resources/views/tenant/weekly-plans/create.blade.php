@extends('layouts.app')

@section('title', 'Create Weekly Plan')

@section('content')
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 0;
        }

        .plan-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .plan-header {
            background: linear-gradient(135deg, #216592 0%, #1a5278 100%);
            color: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(33, 101, 146, 0.2);
        }

        .plan-header h1 {
            margin: 0 0 0.5rem 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .date-range {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .btn-modern {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-outline {
            background: white;
            border: 2px solid #e2e8f0;
            color: #6c757d;
        }

        .btn-outline:hover {
            border-color: #007bff;
            color: #007bff;
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
        }

        .btn-light-modern {
            background: white;
            color: #216592;
            border: 2px solid #216592;
        }

        .btn-light-modern:hover {
            background: #216592;
            color: white;
        }

        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .form-card-header {
            background: linear-gradient(135deg, #216592 0%, #1a5278 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-card-header h4 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .form-card-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2d3748;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: white;
        }

        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .task-card {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.2s ease;
        }

        .task-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.1);
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .task-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }

        .task-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .text-end {
            text-align: right;
        }

        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            font-weight: 500;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>

    <div class="plan-container">
        <!-- Header -->
        <div class="plan-header">
            <h1>📝 Create New Weekly Plan</h1>
            <div class="date-range">{{ $plan->start_date->format('M d, Y') }} - {{ $plan->end_date->format('M d, Y') }}</div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tenant.weekly-plans.store') }}" method="POST">
            @csrf

            <!-- Plan Details Card -->
            <div class="form-card">
                <div class="form-card-header">
                    <h4>📋 Plan Details</h4>
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label for="title" class="form-label">Plan Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}"
                            placeholder="Enter a title for this weekly plan..." required>
                    </div>

                    <div class="form-group">
                        <label for="summary" class="form-label">Weekly Summary/Goals</label>
                        <textarea class="form-control" id="summary" name="summary" rows="4" required
                            placeholder="Describe your goals and objectives for this week...">{{ old('summary', $plan->summary) }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ old('start_date', $plan->start_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ old('end_date', $plan->end_date->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Card -->
            <div class="form-card">
                <div class="form-card-header">
                    <h4>📅 Weekly Tasks</h4>
                    <button type="button" class="btn-modern btn-light-modern btn-sm" id="add-task">
                        <i class="fas fa-plus"></i> Add Task
                    </button>
                </div>
                <div class="form-card-body" id="tasks-container"></div>
            </div>

            <div class="text-end">
                <a href="{{ route('tenant.weekly-plans.index') }}" class="btn-modern btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Plans
                </a>
                <button type="submit" class="btn-modern btn-success-modern">
                    <i class="fas fa-save"></i> Save Weekly Plan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('tasks-container');
            const addButton = document.getElementById('add-task');
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            let taskCount = 0;
            let availableDays = [];

            function generateDays(start, end) {
                if (!start || !end) return [];
                const startDate = new Date(start + 'T00:00');
                const endDate = new Date(end + 'T00:00');
                const days = [];

                if (endDate < startDate) return days;

                const options = { weekday: 'long' };
                let current = new Date(startDate);

                while (current <= endDate) {
                    days.push(current.toLocaleDateString('en-US', options));
                    current.setDate(current.getDate() + 1);
                }
                return days;
            }

            function addTask(taskData = {}) {
                const taskId = taskCount++;
                const dayOptions = availableDays.map(day =>
                    `<option value="${day.toLowerCase()}" ${taskData.day === day.toLowerCase() ? 'selected' : ''}>${day}</option>`
                ).join('');

                const taskHtml = `
                    <div class="task-card" data-id="${taskId}">
                        <div class="task-header">
                            <h5 class="task-title">📝 Task #${taskId + 1}</h5>
                            <button type="button" class="btn-modern btn-danger-modern btn-sm remove-task" data-id="${taskId}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="task-grid">
                            <div class="form-group">
                                <label class="form-label">Task Title</label>
                                <input type="text" class="form-control" name="tasks[${taskId}][title]" value="${taskData.title || ''}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Day</label>
                                <select class="form-select" name="tasks[${taskId}][day]" required>
                                    <option value="">Select day</option>${dayOptions}
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="tasks[${taskId}][priority]" required>
                                    <option value="low" ${taskData.priority === 'low' ? 'selected' : ''}>🟢 Low</option>
                                    <option value="medium" ${taskData.priority === 'medium' ? 'selected' : ''} selected>🟡 Medium</option>
                                    <option value="high" ${taskData.priority === 'high' ? 'selected' : ''}>🔴 High</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="tasks[${taskId}][description]" rows="3">${taskData.description || ''}</textarea>
                        </div>
                    </div>`;
                container.insertAdjacentHTML('beforeend', taskHtml);
            }

            function refreshTaskDays() {
                document.querySelectorAll('select[name$="[day]"]').forEach(select => {
                    const currentValue = select.value;
                    select.innerHTML = `<option value="">Select day</option>` +
                        availableDays.map(day =>
                            `<option value="${day.toLowerCase()}" ${currentValue === day.toLowerCase() ? 'selected' : ''}>${day}</option>`
                        ).join('');
                });
            }

            function updateDays() {
                availableDays = generateDays(startInput.value, endInput.value);
                refreshTaskDays();
            }

            // 👉 Ye line fix hai — page load pe hi default days load ho jayenge
            updateDays();
            addTask();

            startInput.addEventListener('change', updateDays);
            endInput.addEventListener('change', updateDays);

            addButton.addEventListener('click', function() {
                addTask();
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-task') || e.target.closest('.remove-task')) {
                    const button = e.target.closest('.remove-task');
                    const taskId = button.getAttribute('data-id');
                    const taskCard = document.querySelector(`.task-card[data-id="${taskId}"]`);
                    if (container.children.length > 1) {
                        taskCard.remove();
                    } else {
                        alert('You must have at least one task.');
                    }
                }
            });

            document.querySelector('form').addEventListener('submit', function(e) {
                const tasks = document.querySelectorAll('.task-card');
                let isValid = true;

                tasks.forEach(task => {
                    const title = task.querySelector('input[name$="[title]"]');
                    const day = task.querySelector('select[name$="[day]"]');
                    const priority = task.querySelector('select[name$="[priority]"]');

                    if (!title.value.trim() || !day.value || !priority.value) {
                        isValid = false;
                        [title, day, priority].forEach(field => {
                            if (!field.value.trim()) field.classList.add('is-invalid');
                            else field.classList.remove('is-invalid');
                        });
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields for each task.');
                }
            });
        });
    </script>
@endsection
