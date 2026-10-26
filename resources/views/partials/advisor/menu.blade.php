<div class="dashboard-cards">
    <div class="dashboard-card">
        <h3>Assigned Students</h3>
        <p class="record-count">{{ $studentCount }}</p>

        <a href="{{ route('advisor.students.index') }}">
            View Students
        </a>
    </div>

    <div class="dashboard-card">
        <h3>Plans Awaiting Review</h3>
        <p class="record-count">0</p>
    </div>

    <div class="dashboard-card">
        <h3>Needs Attention</h3>
        <p class="record-count">0</p>
    </div>
                

                    
    <div class="dashboard-card">
        <h3>Plans Awaiting Review</h3>
        <p>No plans waiting for review.</p>
    </div>
                    
    <div class="dashboard-card">
        <h3>Students Needing Attention</h3>
        <p>No students flagged.</p>
    </div>     
</div>