<div class="page-header">
    <div>
        <h4><i class="fas fa-tasks me-2"></i>Lead Assignment</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Home</a></li><li class="breadcrumb-item active">Assignment</li></ol></nav>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-4">
        <!-- Filter & Assign Form -->
        <div class="card dashboard-card">
            <div class="card-header"><h6><i class="fas fa-filter me-2"></i>Filter & Assign</h6></div>
            <div class="card-body">
                <form id="filterForm" method="GET" action="<?= BASE_URL ?>/assignment">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Filter by School</label>
                        <select name="school" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Schools</option>
                            <?php foreach ($schools as $s): ?><option value="<?= e($s) ?>" <?= ($filters['school']===$s)?'selected':'' ?>><?= e($s) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Filter by District</label>
                        <select name="district" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Districts</option>
                            <?php foreach ($districts as $d): ?><option value="<?= e($d) ?>" <?= ($filters['district']===$d)?'selected':'' ?>><?= e($d) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Filter by Course</label>
                        <select name="course" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Courses</option>
                            <?php foreach ($courses as $c): ?><option value="<?= e($c) ?>" <?= ($filters['course']===$c)?'selected':'' ?>><?= e($c) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Filter by Status</label>
                        <select name="status" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Status</option>
                            <?php foreach (['New','Contacted','Interested','Follow Up'] as $st): ?><option value="<?= $st ?>" <?= ($filters['status']===$st)?'selected':'' ?>><?= $st ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <a href="<?= BASE_URL ?>/assignment" class="btn btn-outline-secondary w-100 mb-4"><i class="fas fa-redo me-2"></i>Clear Filters</a>
                </form>
                
                <hr>
                
                <form method="POST" action="<?= BASE_URL ?>/assignment/assign" id="assignForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assign to Telecaller</label>
                        <select name="member_id" class="form-select" required>
                            <option value="">Select Telecaller</option>
                            <?php foreach ($members as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['name']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Hidden container for selected lead IDs -->
                    <div id="selectedLeadsContainer"></div>
                    <button type="button" class="btn btn-primary w-100" onclick="submitAssignedLeads()">
                        <i class="fas fa-paper-plane me-2"></i>Assign Selected Leads
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Assign By School Form -->
        <div class="card dashboard-card mt-4">
            <div class="card-header bg-success text-white"><h6 class="text-white mb-0"><i class="fas fa-school me-2"></i>Quick Assign by School</h6></div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/assignment/assign-by-school">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select School</label>
                        <select name="school" class="form-select" required>
                            <option value="">Select School</option>
                            <?php foreach ($schools as $s): ?><option value="<?= e($s) ?>"><?= e($s) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assign to Telecaller</label>
                        <select name="member_id" class="form-select" required>
                            <option value="">Select Telecaller</option>
                            <?php foreach ($members as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['name']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Assign all leads from this school to the selected telecaller?')">
                        <i class="fas fa-bolt me-2"></i>Assign All School Leads
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Leads Table (Filtered Results) -->
    <div class="col-xl-8">
        <div class="card dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-list me-2"></i>Leads (<span id="assignLeadsCount"><?= count($leads) ?></span>)</h6>
                <div>
                    <button class="btn btn-sm btn-outline-primary" onclick="selectAll(true)"><i class="fas fa-check-double me-1"></i>Select All</button>
                    <button class="btn btn-sm btn-outline-secondary ms-1" onclick="selectAll(false)"><i class="fas fa-times me-1"></i>Clear</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height:800px;overflow:auto;">
                    <table class="table table-hover mb-0">
                        <thead class="sticky-top bg-white">
                            <tr>
                                <th><input type="checkbox" id="selectAllCheckbox" onchange="selectAll(this.checked)"></th>
                                <th>Student</th><th>School</th><th>District</th><th>Course</th><th>Status</th><th>Telecaller</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($leads as $l): ?>
                        <tr>
                            <td><input type="checkbox" class="lead-cb" value="<?= $l['id'] ?>"></td>
                            <td><?= e($l['student_name']) ?></td>
                            <td><?= e($l['school_name']) ?></td>
                            <td><?= e($l['district']) ?></td>
                            <td><?= e($l['course_interested']) ?></td>
                            <td><span class="badge-status badge-<?= strtolower(str_replace(' ','',$l['lead_status'])) ?>"><?= e($l['lead_status']) ?></span></td>
                            <td><?= $l['member_name'] ? '<span class="badge bg-primary">'.e($l['member_name']).'</span>' : '<span class="text-muted">Unassigned</span>' ?></td>
                        </tr>
                        <?php endforeach; if(empty($leads)): ?>
                        <tr><td colspan="7"><div class="empty-state">No leads match filters</div></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectAll(check) {
    document.getElementById('selectAllCheckbox').checked = check;
    document.querySelectorAll('.lead-cb').forEach(cb => cb.checked = check);
}
function submitAssignedLeads() {
    const cbs = document.querySelectorAll('.lead-cb:checked');
    if (cbs.length === 0) {
        Swal.fire({icon:'warning', title:'No Leads Selected', text:'Please select at least one lead to assign.'});
        return;
    }
    const container = document.getElementById('selectedLeadsContainer');
    container.innerHTML = '';
    cbs.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'lead_ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });
    document.getElementById('assignForm').submit();
}
</script>
