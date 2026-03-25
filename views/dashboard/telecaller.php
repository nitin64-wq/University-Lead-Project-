<?php
// Telecaller Dashboard View
?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-headset me-2"></i>Telecaller Dashboard</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Home</a></li><li class="breadcrumb-item active">Dashboard</li></ol></nav>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-body">
                <div class="stat-card-icon"><i class="fas fa-list"></i></div>
                <div><h3><?= count($recent) ?></h3><p>Assigned Leads</p></div>
            </div>
            <div class="stat-card-footer"><i class="fas fa-info-circle me-1"></i>Recent leads available</div>
        </div>
    </div>
</div>

<div class="card dashboard-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6><i class="fas fa-clock me-2"></i>My Recent Leads</h6>
        <a href="<?= BASE_URL ?>/leads" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Student</th><th>School</th><th>Contact</th><th>Course</th><th>Temperature</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($recent as $l): ?>
                <tr>
                    <td><strong><?= e($l['student_name']) ?></strong></td>
                    <td><?= e($l['school_name']) ?></td>
                    <td><?= e($l['student_contact']) ?></td>
                    <td><?= e($l['course_interested']) ?></td>
                    <td><span class="badge-<?= strtolower($l['temperature']) ?>"><?= e($l['temperature']) ?></span></td>
                    <td><span class="badge-status badge-<?= strtolower(str_replace(' ','',$l['lead_status'])) ?>"><?= e($l['lead_status']) ?></span></td>
                    <td>
                        <a href="<?= BASE_URL ?>/leads/<?= $l['id'] ?>/edit" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i> Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recent)): ?>
                <tr><td colspan="7" class="text-center py-4 text-muted">No leads assigned recently.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
