<?php
// Telecaller — My Leads List
$statuses  = ['','New','Contacted','Interested','Follow Up','Converted','Not Interested','No Response','Call Not Picked'];
$temps     = ['','Hot','Warm','Cold'];
?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-users me-2" style="color:var(--teal);"></i>My Leads</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tc/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">My Leads</li>
        </ol></nav>
    </div>
    <div class="fw-semibold text-muted" style="font-size:13px;">
        <i class="fas fa-list-ol me-1"></i><?= number_format($result['total']) ?> leads total
    </div>
</div>

<!-- Filters -->
<div class="card dashboard-card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="" class="row g-2 align-items-end">
            <input type="hidden" name="route" value="tc/leads">
            <div class="col-md-3">
                <input type="text" name="search" value="<?= e($filters['search']) ?>" class="form-control form-control-sm" placeholder="🔍 Name or contact…">
            </div>
            <div class="col-md-2">
                <select name="temp" class="form-select form-select-sm">
                    <?php foreach ($temps as $t): ?>
                    <option value="<?= $t ?>" <?= $filters['temp']===$t?'selected':'' ?>><?= $t ?: 'All Temperatures' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <?php foreach ($statuses as $st): ?>
                    <option value="<?= $st ?>" <?= $filters['status']===$st?'selected':'' ?>><?= $st ?: 'All Statuses' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="course" class="form-select form-select-sm">
                    <option value="">All Courses</option>
                    <?php foreach ($courses as $c): ?>
                    <option value="<?= e($c) ?>" <?= $filters['course']===$c?'selected':'' ?>><?= e($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="district" class="form-select form-select-sm">
                    <option value="">All Districts</option>
                    <?php foreach ($districts as $d): ?>
                    <option value="<?= e($d) ?>" <?= $filters['district']===$d?'selected':'' ?>><?= e($d) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button class="btn btn-sm btn-primary w-100" style="background:var(--teal);border-color:var(--teal);"><i class="fas fa-filter"></i></button>
                <a href="<?= BASE_URL ?>/tc/leads" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Leads Table -->
<div class="card dashboard-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Contact</th>
                        <th>School / District</th>
                        <th>Course</th>
                        <th>Temp</th>
                        <th>Status</th>
                        <th>Next Follow-up</th>
                        <th>Last Call</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($result['data'])): ?>
                <tr><td colspan="10" class="text-center py-5">
                    <div class="empty-state"><i class="fas fa-users-slash"></i><h6>No leads found</h6><p>No leads match your filter.</p></div>
                </td></tr>
                <?php else: ?>
                <?php foreach ($result['data'] as $i => $l): ?>
                <tr>
                    <td class="text-muted" style="font-size:12px;"><?= ($result['page']-1)*15+$i+1 ?></td>
                    <td>
                        <strong style="font-size:13px;"><?= e($l['student_name']) ?></strong>
                        <?php if ($l['father_name']): ?><div class="text-muted" style="font-size:11px;">F: <?= e($l['father_name']) ?></div><?php endif; ?>
                    </td>
                    <td>
                        <a href="tel:<?= e($l['student_contact']) ?>" class="tc-call-link"><i class="fas fa-phone me-1"></i><?= e($l['student_contact']) ?></a>
                        <?php if ($l['parent_contact']): ?><div class="text-muted" style="font-size:11px;"><i class="fas fa-user me-1"></i><?= e($l['parent_contact']) ?></div><?php endif; ?>
                    </td>
                    <td>
                        <div style="font-size:13px;"><?= e($l['school_name']) ?></div>
                        <div class="text-muted" style="font-size:11px;"><?= e($l['district']) ?></div>
                    </td>
                    <td style="font-size:13px;"><?= e($l['course_interested']) ?></td>
                    <td><span class="badge-<?= strtolower($l['temperature']) ?>"><?= e($l['temperature']) ?></span></td>
                    <td><span class="badge-status badge-<?= strtolower(str_replace([' ','/'],'',$l['lead_status'])) ?>"><?= e($l['lead_status']) ?></span></td>
                    <td style="font-size:12px;">
                        <?php if ($l['next_follow_up']): ?>
                            <?php
                            $fu = strtotime($l['next_follow_up']);
                            $today = strtotime('today');
                            $diff = $fu - $today;
                            $cls = $diff < 0 ? 'text-danger' : ($diff === 0 ? 'text-warning fw-bold' : 'text-success');
                            ?>
                            <span class="<?= $cls ?>"><i class="fas fa-calendar me-1"></i><?= date('d M Y', $fu) ?></span>
                        <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                    </td>
                    <td style="font-size:12px;" class="text-muted">
                        <?= $l['last_call_date'] ? date('d M', strtotime($l['last_call_date'])) : '—' ?>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>/tc/leads/<?= $l['id'] ?>" class="btn btn-sm" style="background:#e0f2f1;color:#0d9488;border-radius:8px;">
                            <i class="fas fa-phone-alt me-1"></i>Log Call
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Pagination -->
    <?php if ($result['pages'] > 1): ?>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <div class="text-muted" style="font-size:13px;">
            Page <?= $result['page'] ?> of <?= $result['pages'] ?> &mdash; <?= number_format($result['total']) ?> leads
        </div>
        <nav><ul class="pagination pagination-sm mb-0">
            <?php for ($p = 1; $p <= $result['pages']; $p++): ?>
            <li class="page-item <?= $p===$result['page']?'active':'' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page'=>$p,'route'=>'tc/leads'])) ?>"><?= $p ?></a>
            </li>
            <?php endfor; ?>
        </ul></nav>
    </div>
    <?php endif; ?>
</div>
