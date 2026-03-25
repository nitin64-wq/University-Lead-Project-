<?php
// Telecaller — Full-Page Lead Edit
$l   = $lead;
$temp = $l['temperature'] ?? 'Cold';
?>

<div class="page-header">
    <div>
        <h4><i class="fas fa-user-edit me-2" style="color:var(--teal);"></i>Edit Lead</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tc/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tc/leads">My Leads</a></li>
            <li class="breadcrumb-item active"><?= e($l['student_name']) ?></li>
        </ol></nav>
    </div>
    <a href="<?= BASE_URL ?>/tc/leads" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Leads
    </a>
</div>

<!-- Student summary card -->
<div class="card dashboard-card mb-4" style="border-left:4px solid #6366f1;">
    <div class="card-body py-3">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#0ea5e9,#6366f1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:700;flex-shrink:0;">
                <?= strtoupper(substr($l['student_name'],0,1)) ?>
            </div>
            <div>
                <div class="fw-bold" style="font-size:16px;"><?= e($l['student_name']) ?></div>
                <div class="text-muted" style="font-size:13px;">
                    <?php if ($l['father_name']): ?>F: <?= e($l['father_name']) ?> &nbsp;·&nbsp; <?php endif; ?>
                    <a href="tel:<?= e($l['student_contact']) ?>"><i class="fas fa-phone me-1 text-success"></i><?= e($l['student_contact']) ?></a>
                    <?php if ($l['parent_contact']): ?>&nbsp;·&nbsp; <i class="fas fa-user me-1 text-primary"></i><?= e($l['parent_contact']) ?><?php endif; ?>
                </div>
                <div class="text-muted" style="font-size:12px;"><?= e($l['school_name']) ?> &nbsp;·&nbsp; <?= e($l['district']) ?></div>
            </div>
            <div class="ms-auto">
                <?php
                $badge = match($temp) {
                    'HOT'              => '<span style="background:#fef2f2;color:#b91c1c;padding:5px 14px;border-radius:20px;font-weight:700;font-size:13px;">🔥 HOT</span>',
                    'Warm'             => '<span style="background:#fefce8;color:#92400e;padding:5px 14px;border-radius:20px;font-weight:700;font-size:13px;">🌡️ Warm</span>',
                    'Cold'             => '<span style="background:#eff6ff;color:#1d4ed8;padding:5px 14px;border-radius:20px;font-weight:700;font-size:13px;">❄️ Cold</span>',
                    'Not Communicated' => '<span style="background:#f1f5f9;color:#475569;padding:5px 14px;border-radius:20px;font-weight:700;font-size:13px;">📵 Not Communicated</span>',
                    default            => '',
                };
                echo $badge;
                ?>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="<?= BASE_URL ?>/tc/leads/<?= $l['id'] ?>/edit" id="editForm">

    <!-- Tabs -->
    <div class="edit-tabs mb-0">
        <button type="button" class="edit-tab active" data-target="tabStudentInfo">
            <i class="fas fa-user me-2"></i>Student Info
        </button>
        <button type="button" class="edit-tab" data-target="tabLeadStatus">
            <i class="fas fa-chart-line me-2"></i>Lead Status
        </button>
    </div>

    <!-- ─── TAB 1: Student Info ─── -->
    <div id="tabStudentInfo" class="edit-panel active card dashboard-card" style="border-radius:0 12px 12px 12px;">
        <div class="card-body">
            <div class="alert alert-warning d-flex align-items-center gap-2" style="font-size:13px;border-radius:10px;">
                <i class="fas fa-exclamation-triangle"></i>
                Correct any wrong student information below — changes are saved to the database.
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="edit-label">Student Name</label>
                    <input type="text" name="student_name" class="form-control edit-input"
                           value="<?= e($l['student_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="edit-label">Father's Name</label>
                    <input type="text" name="father_name" class="form-control edit-input"
                           value="<?= e($l['father_name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="edit-label"><i class="fas fa-phone me-1 text-success"></i>Student Contact</label>
                    <input type="text" name="student_contact" class="form-control edit-input"
                           value="<?= e($l['student_contact']) ?>" maxlength="15">
                </div>
                <div class="col-md-6">
                    <label class="edit-label"><i class="fas fa-phone me-1 text-primary"></i>Parent Contact</label>
                    <input type="text" name="parent_contact" class="form-control edit-input"
                           value="<?= e($l['parent_contact']) ?>" maxlength="15">
                </div>
                <div class="col-md-12">
                    <label class="edit-label"><i class="fas fa-school me-1" style="color:#8b5cf6;"></i>School Name</label>
                    <input type="text" name="school_name" class="form-control edit-input"
                           value="<?= e($l['school_name']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="edit-label">District</label>
                    <input type="text" name="district" class="form-control edit-input"
                           value="<?= e($l['district']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="edit-label">Village / City</label>
                    <input type="text" name="village" class="form-control edit-input"
                           value="<?= e($l['village']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="edit-label">Stream</label>
                    <input type="text" name="stream" class="form-control edit-input"
                           value="<?= e($l['stream']) ?>" placeholder="e.g. Science, Commerce">
                </div>
                <div class="col-md-6">
                    <label class="edit-label">Category</label>
                    <input type="text" name="category" class="form-control edit-input"
                           value="<?= e($l['category']) ?>" placeholder="e.g. General, OBC, SC/ST">
                </div>
            </div>
        </div>
    </div>

    <!-- ─── TAB 2: Lead Status ─── -->
    <div id="tabLeadStatus" class="edit-panel card dashboard-card" style="border-radius:0 12px 12px 12px;">
        <div class="card-body">

            <!-- Row 1: Status / Course / Availability / Call Info -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="edit-label">Status</label>
                    <select name="temperature" id="eTemp" class="form-select edit-input" onchange="toggleSections(this.value)">
                        <option value="HOT"              <?= $temp==='HOT'?'selected':'' ?>>🔥 HOT</option>
                        <option value="Warm"             <?= $temp==='Warm'?'selected':'' ?>>🌡️ Warm</option>
                        <option value="Cold"             <?= $temp==='Cold'?'selected':'' ?>>❄️ Cold</option>
                        <option value="Not Communicated" <?= $temp==='Not Communicated'?'selected':'' ?>>📵 Not Communicated</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="edit-label">Course (Interested)</label>
                    <input type="text" name="course_interested" class="form-control edit-input"
                           value="<?= e($l['course_interested']) ?>" placeholder="e.g. B.Tech, MBA…">
                </div>
                <div class="col-md-3">
                    <label class="edit-label">Date of Availability</label>
                    <input type="date" name="availability_date" class="form-control edit-input"
                           value="<?= e($l['availability_date']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="edit-label"><i class="fas fa-redo me-1"></i>Fresh / Follow up Call</label>
                    <select name="call_type" class="form-select edit-input">
                        <option value="Fresh Call"   <?= ($l['call_type']??'')==='Fresh Call'?'selected':'' ?>>📞 Fresh Call</option>
                        <option value="Follow up call" <?= ($l['call_type']??'')==='Follow up call'?'selected':'' ?>>🔄 Follow up call</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="edit-label"><i class="fas fa-clock me-1"></i>Duration of Call <small>(mins)</small></label>
                    <input type="number" name="call_duration" class="form-control edit-input"
                           value="<?= e($l['call_duration']) ?>" step="0.1" min="0" placeholder="e.g. 2.5">
                </div>
            </div>

            <!-- Warm section -->
            <div id="warmSection" class="cond-section cond-warm <?= $temp==='Warm'?'show':'' ?>">
                <div class="cond-label"><i class="fas fa-temperature-high me-2"></i>In case of Warm</div>
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="edit-label">Warm Situation</label>
                        <select name="warm_case" class="form-select edit-input">
                            <option value="">— Select —</option>
                            <?php foreach (['Not Decided Yet','Discuss with Family','Communicated with Parents (Student Not Available)','Follow up Required'] as $opt): ?>
                            <option value="<?= $opt ?>" <?= ($l['warm_case']??'')===$opt?'selected':'' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="edit-label">Warm Level</label>
                        <select name="warm_level" class="form-select edit-input">
                            <option value="">— Level —</option>
                            <option value="Level-1" <?= ($l['warm_level']??'')==='Level-1'?'selected':'' ?>>Level-1</option>
                            <option value="Level-2" <?= ($l['warm_level']??'')==='Level-2'?'selected':'' ?>>Level-2</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Follow-up (HOT / Warm) -->
            <div id="followupSection" class="cond-section cond-hot <?= ($temp==='HOT'||$temp==='Warm')?'show':'' ?>">
                <div class="cond-label"><i class="fas fa-calendar-check me-2"></i>Next Date of Follow Up <small>(Hot/Warm)</small></div>
                <input type="date" name="next_follow_up" class="form-control edit-input" style="max-width:260px;"
                       value="<?= e($l['next_follow_up']) ?>">
            </div>

            <!-- Not Communicated section -->
            <div id="ncSection" class="cond-section cond-nc <?= $temp==='Not Communicated'?'show':'' ?>">
                <div class="cond-label"><i class="fas fa-phone-slash me-2"></i>If Not Communicated — Reason</div>
                <select name="not_communicated_reason" class="form-select edit-input">
                    <option value="">— Select reason —</option>
                    <?php foreach (['Phone Switch Off','Call Not Connected','Call Not Recieved','Invalid Number','incomming off','number busy','out of services'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($l['not_communicated_reason']??'')===$opt?'selected':'' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Cold section -->
            <div id="coldSection" class="cond-section cond-cold <?= $temp==='Cold'?'show':'' ?>">
                <div class="cond-label"><i class="fas fa-snowflake me-2"></i>If Cold — Reason</div>
                <select name="cold_reason" id="eColdReason" class="form-select edit-input mb-2" onchange="toggleNoPursue(this.value)">
                    <option value="">— Select reason —</option>
                    <?php foreach (['Admission Taken in Other College',"Don't Want to Pursue Further Study",'Call Disconnected Immediately','Communicated Very Rudely','WRONG NO.','Course not available','Student now in 11th class'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($l['cold_reason']??'')===$opt?'selected':'' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
                <div id="noPursueGroup" style="display:<?= ($l['cold_reason']??'')==="Don't Want to Pursue Further Study"?'block':'none' ?>;">
                    <label class="edit-label">Reason if Don't Want to Pursue Further Study</label>
                    <input type="text" name="no_pursue_reason" class="form-control edit-input"
                           value="<?= e($l['no_pursue_reason'] ?? '') ?>" placeholder="Explain reason…">
                </div>
            </div>

            <!-- Remarks -->
            <div class="mt-3">
                <label class="edit-label"><i class="fas fa-comment me-1"></i>Remarks / Notes</label>
                <textarea name="remarks" class="form-control edit-input" rows="3"
                          placeholder="Add any call notes…"><?= e($l['remarks'] ?? '') ?></textarea>
            </div>

        </div>
    </div>

    <!-- Next Student Indicator + Save Bar -->
    <div class="save-bar">
        <div class="next-indicator">
            <?php if ($nextId): ?>
            <span style="font-size:12px;color:#64748b;">
                <i class="fas fa-arrow-right me-1" style="color:#6366f1;"></i>
                Next: <strong>Student #<?= $nextId ?></strong>
            </span>
            <a href="<?= BASE_URL ?>/tc/leads/<?= $nextId ?>/edit"
               class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:12px;">
                Skip &rarr;
            </a>
            <?php else: ?>
            <span style="font-size:12px;color:#10b981;"><i class="fas fa-check-circle me-1"></i>Last student in list</span>
            <?php endif; ?>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/tc/leads" class="btn btn-outline-secondary" style="border-radius:8px;">
                <i class="fas fa-list me-1"></i>All Leads
            </a>
            <button type="submit" class="btn save-btn">
                <i class="fas fa-save me-2"></i>
                <?= $nextId ? 'Save &amp; Next →' : 'Save Changes' ?>
            </button>
        </div>
    </div>

</form>

<style>
.edit-tabs { display:flex; gap:0; }
.edit-tab {
    padding:10px 22px; border:none; background:#e2e8f0;
    font-size:13px; font-weight:700; color:#64748b; cursor:pointer;
    border-radius:12px 12px 0 0; margin-right:4px; transition:all .2s;
    border-bottom:3px solid transparent;
}
.edit-tab.active { background:#fff; color:#6366f1; border-color:#6366f1; box-shadow:0 -2px 8px rgba(99,102,241,.1); }
.edit-tab:hover:not(.active) { background:#f1f5f9; color:#334155; }

.edit-panel { display:none; }
.edit-panel.active { display:block; }

.edit-label {
    font-size:11px; font-weight:700; color:#64748b;
    text-transform:uppercase; letter-spacing:.4px; margin-bottom:5px; display:block;
}
.edit-input {
    border-radius:10px; font-size:13px;
    border:1.5px solid #e2e8f0; transition:border-color .2s;
}
.edit-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); }

/* Conditional sections */
.cond-section { border-radius:12px; padding:18px; margin-bottom:16px; display:none; }
.cond-section.show { display:block; }
.cond-label { font-size:12px; font-weight:700; margin-bottom:14px; }
.cond-warm { background:#fefce8; border:1.5px solid #fde68a; }
.cond-warm .cond-label { color:#92400e; }
.cond-hot  { background:#f0fdf4; border:1.5px solid #bbf7d0; }
.cond-hot .cond-label  { color:#15803d; }
.cond-cold { background:#eff6ff; border:1.5px solid #bfdbfe; }
.cond-cold .cond-label { color:#1d4ed8; }
.cond-nc   { background:#f8fafc; border:1.5px solid #cbd5e1; }
.cond-nc .cond-label   { color:#475569; }

/* Save bar */
.save-bar {
    display:flex; justify-content:space-between; align-items:center; gap:12px;
    padding:16px 0 32px;
}
.next-indicator {
    display:flex; align-items:center; gap:10px;
    background:#f8fafc; border:1.5px solid #e2e8f0;
    border-radius:10px; padding:8px 16px;
}
.save-btn {
    background:linear-gradient(135deg,#0ea5e9,#6366f1);
    color:#fff; font-weight:700; border:none; border-radius:10px;
    padding:10px 30px; font-size:14px;
    transition:transform .15s, box-shadow .15s;
}
.save-btn:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(99,102,241,.35); color:#fff; }
</style>

<script>
/* Tab switching */
document.querySelectorAll('.edit-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.edit-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.edit-panel').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(this.dataset.target).classList.add('active');
    });
});

/* Status → show/hide conditional sections */
function toggleSections(status) {
    document.getElementById('warmSection').classList.toggle('show',    status === 'Warm');
    document.getElementById('followupSection').classList.toggle('show', status === 'HOT' || status === 'Warm');
    document.getElementById('ncSection').classList.toggle('show',       status === 'Not Communicated');
    document.getElementById('coldSection').classList.toggle('show',     status === 'Cold');
}

function toggleNoPursue(val) {
    document.getElementById('noPursueGroup').style.display =
        val === "Don't Want to Pursue Further Study" ? 'block' : 'none';
}

// Run on load
toggleSections('<?= $temp ?>');
</script>
