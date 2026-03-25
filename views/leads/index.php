<?php $r = $result; ?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-list-alt me-2"></i>Leads List</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Home</a></li><li class="breadcrumb-item active">Leads</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/leads/import" class="btn btn-success btn-sm"><i class="fas fa-file-import me-1"></i>Import</a>
        <a href="<?= BASE_URL ?>/leads/create" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add Lead</a>
    </div>
</div>

<!-- Filters -->
<div class="card dashboard-card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/leads" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">School</label>
                <select name="school" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Schools</option>
                    <?php foreach ($schools as $s): ?><option value="<?= e($s) ?>" <?= ($filters['school']===$s)?'selected':'' ?>><?= e($s) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">District</label>
                <select name="district" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Districts</option>
                    <?php foreach ($districts as $d): ?><option value="<?= e($d) ?>" <?= ($filters['district']===$d)?'selected':'' ?>><?= e($d) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Course</label>
                <select name="course" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Courses</option>
                    <?php foreach ($courses as $c): ?><option value="<?= e($c) ?>" <?= ($filters['course']===$c)?'selected':'' ?>><?= e($c) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Temperature</label>
                <select name="temp" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php foreach (['Hot','Warm','Cold'] as $t): ?><option value="<?= $t ?>" <?= ($filters['temp']===$t)?'selected':'' ?>><?= $t ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php foreach (['New','Contacted','Interested','Follow Up','Converted','Not Interested'] as $st): ?><option value="<?= $st ?>" <?= ($filters['status']===$st)?'selected':'' ?>><?= $st ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end gap-1">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search" value="<?= e($filters['search']) ?>">
                <a href="<?= BASE_URL ?>/leads" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card dashboard-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Student</th><th>School</th><th>District</th><th>Contact</th><th>Course</th><th>Temp</th><th>Status</th><th>Team</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if (empty($r['data'])): ?>
                <tr><td colspan="10"><div class="empty-state"><i class="fas fa-inbox"></i><h6>No leads found</h6><p>Try adjusting filters or import new leads.</p></div></td></tr>
                <?php else: foreach ($r['data'] as $l): ?>
                <tr>
                    <td class="text-muted small"><?= $l['id'] ?></td>
                    <td><strong><?= e($l['student_name']) ?></strong><br><small class="text-muted"><?= e($l['father_name']) ?></small></td>
                    <td><?= e($l['school_name']) ?></td>
                    <td><?= e($l['district']) ?></td>
                    <td><?= e($l['student_contact']) ?></td>
                    <td><?= e($l['course_interested']) ?></td>
                    <td><span class="badge-<?= strtolower($l['temperature']) ?>"><?= e($l['temperature']) ?></span></td>
                    <td><span class="badge-status badge-<?= strtolower(str_replace(' ','',$l['lead_status'])) ?>"><?= e($l['lead_status']) ?></span></td>
                    <td><?= $l['team_name'] ? '<span class="badge bg-primary">'.e($l['team_name']).'</span>' : '<span class="text-muted">Unassigned</span>' ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/leads/<?= $l['id'] ?>" class="action-btn btn-view" title="View details & call history" style="color:var(--primary);"><i class="fas fa-eye"></i></a>
                        <a href="<?= BASE_URL ?>/leads/<?= $l['id'] ?>/edit" class="action-btn btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="<?= BASE_URL ?>/leads/<?= $l['id'] ?>/delete" class="action-btn btn-delete btn-delete-confirm" title="Delete"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center p-3">
            <small class="text-muted">Showing <?= count($r['data']) ?> of <?= $r['total'] ?> leads</small>
            <?php if ($r['pages'] > 1): ?>
            <nav><ul class="pagination pagination-sm mb-0">
                <?php for ($p = 1; $p <= $r['pages']; $p++): $active = $p==$r['page']?'active':''; ?>
                <li class="page-item <?= $active ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$p])) ?>"><?= $p ?></a>
                </li>
                <?php endfor; ?>
            </ul></nav>
            <?php endif; ?>
        </div>
    </div>
</div>
