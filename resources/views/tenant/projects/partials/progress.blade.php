<div class="col-md-3 project-card-container">
    <div class="project-card project-progress-card text-blue">
        <div class="project-card-body">
            <!-- Dropdown with specific percentage values -->
            <h5 class="project-card-title">
                <select class="form-control" name="progress_percentage" id="progress_percentage">
                    <option value="0" {{ $progress == 0 ? 'selected' : '' }}>0%</option>
                    <option value="25" {{ $progress == 25 ? 'selected' : '' }}>25%</option>
                    <option value="50" {{ $progress == 50 ? 'selected' : '' }}>50%</option>
                    <option value="75" {{ $progress == 75 ? 'selected' : '' }}>75%</option>
                    <option value="100" {{ $progress == 100 ? 'selected' : '' }}>100%</option>
                </select>
            </h5>
            <p class="project-card-text">Progress</p>
        </div>
    </div>
</div>