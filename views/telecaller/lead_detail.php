<?php
// Telecaller — Lead Detail + Call Logging
$statusOptions = ['New','Contacted','Interested','Follow Up','Converted','Not Interested','No Response','Call Not Picked'];
$tempOptions   = ['Hot','Warm','Cold'];
$warmOptions   = ['','Low','Medium','High'];
$typeOptions   = ['Fresh','Follow Up'];
$admOptions    = ['Pending','Done','Cancelled'];
?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-user-circle me-2" style="color:var(--teal);"></i><?= e($lead['student_name']) ?></h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tc/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tc/leads">My Leads</a></li>
            <li class="breadcrumb-item active">Lead Detail</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <span class="badge-<?= strtolower($lead['temperature']) ?>" style="font-size:14px;padding:8px 18px;"><?= e($lead['temperature']) ?></span>
        <span class="badge-status badge-<?= strtolower(str_replace([' ','/'],'',$lead['lead_status'])) ?>" style="font-size:14px;padding:8px 18px;"><?= e($lead['lead_status']) ?></span>
    </div>
</div>

<div class="row g-4">
    <!-- Left: Student Info + Call History -->
    <div class="col-xl-5">

        <!-- Student Info Card -->
        <div class="card dashboard-card mb-4">
            <div class="card-header"><h6><i class="fas fa-id-card me-2" style="color:var(--teal);"></i>Student Information</h6></div>
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
                <?php if ($lead['remarks']): ?>
                <div class="mt-3 p-3" style="background:#f0fdf4;border-radius:10px;border-left:4px solid var(--success);">
                    <label style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);font-weight:700;">Last Remarks</label>
                    <p class="mb-0 mt-1" style="font-size:13px;"><?= nl2br(e($lead['remarks'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Call History -->
        <div class="card dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-history me-2" style="color:var(--purple);"></i>Call History</h6>
                <span class="badge bg-secondary"><?= count($logs) ?> logs</span>
            </div>
            <div class="card-body p-0" style="max-height:350px;overflow-y:auto;">
                <?php if (empty($logs)): ?>
                <div class="empty-state py-4"><i class="fas fa-phone-slash"></i><h6>No call logs yet</h6></div>
                <?php else: foreach ($logs as $log): ?>
                <div class="call-log-item">
                    <div class="call-log-header">
                        <div>
                            <span class="call-log-type <?= strtolower($log['call_type'])==='fresh'?'fresh':'followup' ?>">
                                <i class="fas fa-<?= $log['call_type']==='Fresh'?'phone':'redo' ?> me-1"></i><?= e($log['call_type']) ?>
                            </span>
                            <?php if ($log['call_duration']): ?>
                            <span class="text-muted ms-2" style="font-size:11px;"><i class="fas fa-clock me-1"></i><?= e($log['call_duration']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="text-end">
                            <span class="badge-status badge-<?= strtolower(str_replace([' ','/'],'',$log['lead_status'])) ?>"><?= e($log['lead_status']) ?></span>
                            <div class="text-muted" style="font-size:11px;"><?= date('d M Y, h:i A', strtotime($log['created_at'])) ?></div>
                        </div>
                    </div>
                    <?php if ($log['remarks']): ?>
                    <div class="call-log-remarks"><?= nl2br(e($log['remarks'])) ?></div>
                    <?php endif; ?>
                    <?php if ($log['next_follow_up']): ?>
                    <div style="font-size:11px;color:var(--warning);margin-top:4px;"><i class="fas fa-calendar me-1"></i>Follow-up: <?= date('d M Y', strtotime($log['next_follow_up'])) ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>

    <!-- Right: Log New Call Form -->
    <div class="col-xl-7">
        <div class="card dashboard-card" style="position:sticky;top:80px;">
            <div class="card-header" style="background:linear-gradient(135deg,var(--teal),#0f766e);color:#fff;border-radius:12px 12px 0 0;">
                <h6 class="text-white mb-0"><i class="fas fa-phone-alt me-2"></i>📞 Log New Call</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/tc/leads/<?= $lead['id'] ?>/call" id="callForm">

                    <!-- Row 1: Type + Duration -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Call Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <?php foreach ($typeOptions as $t): ?>
                                <div class="tc-radio-card flex-fill" onclick="selectRadio('call_type','<?= $t ?>', this)">
                                    <input type="radio" name="call_type" value="<?= $t ?>" id="ct_<?= $t ?>" <?= $lead['call_type']===$t?'checked':'' ?> hidden>
                                    <i class="fas fa-<?= $t==='Fresh'?'phone':'redo' ?>"></i><span><?= $t ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Call Duration</label>
                            <input type="text" name="call_duration" class="form-control" placeholder="e.g. 3 min 20 sec" value="<?= e($lead['call_duration']) ?>">
                        </div>
                    </div>

                    <!-- Row 2: Lead Status -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Lead Status <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php
                            $statusColors = ['New'=>'secondary','Contacted'=>'primary','Interested'=>'success','Follow Up'=>'warning','Converted'=>'success','Not Interested'=>'danger','No Response'=>'secondary','Call Not Picked'=>'info'];
                            foreach ($statusOptions as $st):
                                $col = $statusColors[$st] ?? 'secondary';
                            ?>
                            <div class="tc-radio-card tc-status-card" onclick="selectRadio('lead_status','<?= $st ?>', this)" data-color="<?= $col ?>">
                                <input type="radio" name="lead_status" value="<?= $st ?>" <?= $lead['lead_status']===$st?'checked':'' ?> hidden>
                                <span><?= $st ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Row 3: Temperature -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Temperature <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <?php
                            $tempIcons = ['Hot'=>'fire','Warm'=>'temperature-half','Cold'=>'snowflake'];
                            foreach ($tempOptions as $t):
                            ?>
                            <div class="tc-radio-card tc-temp-<?= strtolower($t) ?> flex-fill" onclick="selectRadio('temperature','<?= $t ?>', this)">
                                <input type="radio" name="temperature" value="<?= $t ?>" <?= $lead['temperature']===$t?'checked':'' ?> hidden>
                                <i class="fas fa-<?= $tempIcons[$t] ?>"></i><span><?= $t ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Row 4: Warm Level + Admission -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Warm Level</label>
                            <select name="warm_level" class="form-select">
                                <?php foreach ($warmOptions as $w): ?>
                                <option value="<?= $w ?>" <?= $lead['warm_level']===$w?'selected':'' ?>><?= $w ?: 'Select level' ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Admission Status</label>
                            <select name="admission_status" class="form-select">
                                <?php foreach ($admOptions as $a): ?>
                                <option value="<?= $a ?>" <?= $lead['admission_status']===$a?'selected':'' ?>><?= $a ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Row 5: Next Follow-up -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;"><i class="fas fa-calendar me-1" style="color:var(--teal);"></i>Next Follow-up Date</label>
                        <input type="date" name="next_follow_up" class="form-control" value="<?= e($lead['next_follow_up']) ?>" min="<?= date('Y-m-d') ?>">
                    </div>

                    <!-- Row 6: Remarks -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:13px;"><i class="fas fa-comment me-1" style="color:var(--purple);"></i>Remarks</label>
                        <textarea name="remarks" rows="4" class="form-control" placeholder="Write call notes, student response, what was discussed…"><?= e($lead['remarks']) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-lg flex-fill fw-bold" style="background:var(--teal);color:#fff;border-radius:12px;">
                            <i class="fas fa-save me-2"></i>Save Call Log
                        </button>
                        <a href="<?= BASE_URL ?>/tc/leads" class="btn btn-lg btn-outline-secondary" style="border-radius:12px;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
// Radio card selection
function selectRadio(name, value, el) {
    document.querySelectorAll(`[onclick*="selectRadio('${name}'"]`).forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.querySelector(`input[name="${name}"][value="${value}"]`).checked = true;
}
// Highlight initially checked
document.querySelectorAll('.tc-radio-card input[type="radio"]:checked').forEach(inp => {
    inp.closest('.tc-radio-card').classList.add('selected');
});
</script>
<?php $extraJs = ob_get_clean(); ?>
