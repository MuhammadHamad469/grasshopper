<div class="task-item">
    @if($index > 0 || (isset($plan) && $plan->tasks->count() > 1))
        <button type="button" class="remove-task-btn" onclick="removeTask(this)">×</button>
    @endif
    
    <div class="form-group">
        <label for="tasks_{{ $index }}_title">Task Title</label>
        <input type="text" 
               id="tasks_{{ $index }}_title" 
               name="tasks[{{ $index }}][title]" 
               class="form-control" 
               value="{{ old('tasks.'.$index.'.title', isset($task) ? $task->title : '') }}" 
               required>
    </div>
    
    <div class="form-group">
        <label for="tasks_{{ $index }}_description">Description (Optional)</label>
        <textarea id="tasks_{{ $index }}_description" 
                  name="tasks[{{ $index }}][description]" 
                  class="form-control" 
                  rows="2">{{ old('tasks.'.$index.'.description', isset($task) ? $task->description : '') }}</textarea>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <div class="form-group">
            <label for="tasks_{{ $index }}_day">Day</label>
            <select id="tasks_{{ $index }}_day" 
                    name="tasks[{{ $index }}][day]" 
                    class="form-control" 
                    required>
                @foreach($days as $day)
                    <option value="{{ strtolower($day) }}" 
                            {{ (old('tasks.'.$index.'.day', isset($task) ? $task->day : '') == strtolower($day)) ? 'selected' : '' }}>
                        {{ $day }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="tasks_{{ $index }}_priority">Priority</label>
            <select id="tasks_{{ $index }}_priority" 
                    name="tasks[{{ $index }}][priority]" 
                    class="form-control" 
                    required>
                <option value="low" {{ (old('tasks.'.$index.'.priority', isset($task) ? $task->priority : 'medium') == 'low') ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ (old('tasks.'.$index.'.priority', isset($task) ? $task->priority : 'medium') == 'medium') ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ (old('tasks.'.$index.'.priority', isset($task) ? $task->priority : 'medium') == 'high') ? 'selected' : '' }}>High</option>
            </select>
        </div>
    </div>
</div>
