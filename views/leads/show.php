<?php
// Admin — Lead Details & Call History View
?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-user-circle me-2" style="color:var(--primary);"></i><?= e($lead['student_name']) ?></h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/leads">Leads</a></li>
            <li class="breadcrumb-item active">Lead Details</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <span class="badge-<?= strtolower($lead['temperature']) ?>" style="font-size:14px;padding:8px 18px;"><?= e($lead['temperature']) ?></span>
        <span class="badge-status badge-<?= strtolower(str_replace([' ','/'],'',$lead['lead_status'])) ?>" style="font-size:14px;padding:8px 18px;"><?= e($lead['lead_status']) ?></span>
    </div>
</div>

<div class="row g-4">
    <!-- Left: Student Info -->
    <div class="col-xl-6">
        <div class="card dashboard-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-id-card me-2" style="color:var(--primary);"></i>Student Information</h6>
                <a href="<?= BASE_URL ?>/leads/<?= $lead['id'] ?>/edit" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit me-1"></i>Edit Lead</a>
            </div>
            <div class="card-body">
                <div class="lead-detail-grid">
                    <div class="lead-detail-item"><label>Student Name</label><span><?= e($lead['student_name']) ?></span></div>
                    <div class="lead-detail-item"><label>Father's Name</label><span><?= e($lead['father_name']) ?: '—' ?></span></div>
                    <div class="lead-detail-item">
                        <label>Student Contact</label>
                        <span><a href="tel:<?= e($lead['student_contact']) ?>" class="tc-call-link"><i class="fas fa-phone me-1"></i><?= e($lead['student_contact']) ?></a></span>
                    </div>
                    <div class="lead-detail-item">
                        <label>Parent Contact</label>
                        <span><?= $lead['parent_contact'] ? '<a href="tel:'.e($lead['parent_contact']).'" class="tc-call-link"><i class="fas fa-phone me-1"></i>'.e($lead['parent_contact']).'</a>' : '—' ?></span>
                    </div>
                    <div class="lead-detail-item"><label>School</label><span><?= e($lead['school_name']) ?: '—' ?></span></div>
                    <div class="lead-detail-item"><label>District</label><span><?= e($lead['district']) ?: '—' ?></span></div>
                    <div class="lead-detail-item"><label>Village</label><span><?= e($lead['village']) ?: '—' ?></span></div>
                    <div class="lead-detail-item"><label>Stream</label><span><?= e($lead['stream']) ?: '—' ?></span></div>
                    <div class="lead-detail-item"><label>Category</label><span><?= e($lead['category']) ?: '—' ?></span></div>
                    <div class="lead-detail-item"><label>Course Interested</label><span><?= e($lead['course_interested']) ?: '—' ?></span></div>
                    <div class="lead-detail-item"><label>Admission Status</label>
                        <span class="<?= $lead['admission_status']==='Done'?'text-success fw-bold':'' ?>"><?= e($lead['admission_status']) ?></span>
                    </div>
                    <div class="lead-detail-item"><label>Next Follow-up</label>
                        <span><?= $lead['next_follow_up'] ? date('d M Y', strtotime($lead['next_follow_up'])) : '—' ?></span>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <h6 class="mb-3"><i class="fas fa-sitemap me-2 text-muted"></i>Assignment Details</h6>
                <div class="lead-detail-grid">
                    <div class="lead-detail-item"><label>Assigned Team</label>
                        <span><?= $lead['assigned_team_id'] ? e((TeamModel::find($lead['assigned_team_id']))['name'] ?? 'Unknown Team') : '<em class="text-muted">Unassigned</em>' ?></span>
                    </div>
                    <div class="lead-detail-item"><label>Assigned Member</label>
                        <span><?= $lead['assigned_member_id'] ? e((MemberModel::find($lead['assigned_member_id']))['name'] ?? 'Unknown Member') : '<em class="text-muted">Unassigned</em>' ?></span>
                    </div>
                </div>

                <?php if ($lead['remarks']): ?>
                <div class="mt-4 p-3" style="background:#f8fafc;border-radius:10px;border-left:4px solid var(--primary);">
                    <label style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);font-weight:700;">Latest Remarks</label>
                    <p class="mb-0 mt-1" style="font-size:13px;"><?= nl2br(e($lead['remarks'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right: Call History -->
    <div class="col-xl-6">
        <div class="card dashboard-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-history me-2" style="color:var(--purple);"></i>Telecaller Updates (Call History)</h6>
                <span class="badge bg-secondary"><?= count($logs) ?> logs</span>
            </div>
            <div class="card-body p-0" style="max-height:650px;overflow-y:auto;">
                <?php if (empty($logs)): ?>
                <div class="empty-state py-5">
                    <i class="fas fa-phone-slash mb-3" style="font-size:32px;color:var(--border-color);"></i>
                    <h6>No telecaller updates yet</h6>
                    <p class="text-muted small">Once a telecaller logs a call, it will appear here.</p>
                </div>
                <?php else: foreach ($logs as $log): ?>
                <div class="call-log-item p-3 border-bottom">
                    <div class="call-log-header d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="call-log-type <?= strtolower($log['call_type'])==='fresh'?'fresh':'followup' ?> me-2" style="font-size:12px;padding:4px 8px;border-radius:6px;background:var(--body-bg);font-weight:600;">
                                <i class="fas fa-<?= $log['call_type']==='Fresh'?'phone':'redo' ?> me-1"></i><?= e($log['call_type']) ?>
                            </span>
                            <?php if ($log['call_duration']): ?>
                            <span class="text-muted" style="font-size:12px;"><i class="fas fa-clock me-1"></i><?= e($log['call_duration']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="text-end">
                            <span class="badge-status badge-<?= strtolower(str_replace([' ','/'],'',$log['lead_status'])) ?>" style="font-size:11px;"><?= e($log['lead_status']) ?></span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-primary fw-semibold"><i class="fas fa-user-headset me-1"></i><?= e($log['telecaller_name']) ?></small>
                        <small class="text-muted"><?= date('d M Y, h:i A', strtotime($log['created_at'])) ?></small>
                    </div>

                    <?php if ($log['remarks']): ?>
                    <div class="call-log-remarks p-2 mt-2" style="background:#f1f5f9;border-radius:8px;font-size:13px;color:var(--text-dark);">
                        <?= nl2br(e($log['remarks'])) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($log['next_follow_up']): ?>
                    <div style="font-size:12px;color:var(--warning);font-weight:500;margin-top:8px;">
                        <i class="fas fa-calendar-check me-1"></i>Scheduled Follow-up: <?= date('d M Y', strtotime($log['next_follow_up'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>
