<?php
// Telecaller — My Leads (clean table + tabbed edit modal)
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
<div class="card dashboard-card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="" class="row g-2 align-items-end">
            <input type="hidden" name="route" value="tc/leads">
            <div class="col-md-3">
                <input type="text" name="search" value="<?= e($filters['search']) ?>" class="form-control form-control-sm" placeholder="🔍 Name, contact…">
            </div>
            <div class="col-md-2">
                <select name="temp" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <?php foreach (['HOT','Warm','Cold','Not Communicated'] as $t): ?>
                    <option value="<?= $t ?>" <?= ($filters['temp']===$t)?'selected':'' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="district" class="form-select form-select-sm">
                    <option value="">All Districts</option>
                    <?php foreach ($districts as $d): ?>
                    <option value="<?= e($d) ?>" <?= ($filters['district']===$d)?'selected':'' ?>><?= e($d) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button class="btn btn-sm btn-primary" style="background:var(--teal);border-color:var(--teal);"><i class="fas fa-filter"></i> Filter</button>
                <a href="<?= BASE_URL ?>/tc/leads" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Leads Table -->
<div class="card dashboard-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:13px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Contact</th>
                        <th>School / District</th>
                        <th>Status</th>
                        <th>Course</th>
                        <th>Warm Level</th>
                        <th>Next Follow-up</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($result['data'])): ?>
                <tr><td colspan="9" class="text-center py-5">
                    <div class="empty-state"><i class="fas fa-users-slash"></i><h6>No leads found</h6><p>No leads match your filter.</p></div>
                </td></tr>
                <?php else: ?>
                <?php foreach ($result['data'] as $i => $l): ?>
                <?php
                    $temp = $l['temperature'] ?? 'Cold';
                    $tempBadge = match($temp) {
                        'HOT'              => '<span class="tc-badge tc-badge-hot">🔥 HOT</span>',
                        'Warm'             => '<span class="tc-badge tc-badge-warm">🌡️ Warm</span>',
                        'Cold'             => '<span class="tc-badge tc-badge-cold">❄️ Cold</span>',
                        'Not Communicated' => '<span class="tc-badge tc-badge-nc">📵 Not Comm.</span>',
                        default            => '<span class="text-muted">—</span>',
                    };
                    $fuDate = $l['next_follow_up'] ?? '';
                    $fuCls  = 'text-muted';
                    $fuText = '—';
                    if ($fuDate) {
                        $diff   = strtotime($fuDate) - strtotime('today');
                        $fuCls  = $diff < 0 ? 'text-danger fw-bold' : ($diff === 0 ? 'text-warning fw-bold' : 'text-success');
                        $fuText = '<i class="fas fa-calendar me-1"></i>' . date('d M Y', strtotime($fuDate));
                    }
                    // JSON payload for modal
                    $payload = json_encode([
                        'id'             => $l['id'],
                        'name'           => $l['student_name'],
                        'father_name'    => $l['father_name']             ?? '',
                        's_contact'      => $l['student_contact']         ?? '',
                        'p_contact'      => $l['parent_contact']          ?? '',
                        'school'         => $l['school_name']             ?? '',
                        'district'       => $l['district']                ?? '',
                        'village'        => $l['village']                 ?? '',
                        'stream'         => $l['stream']                  ?? '',
                        'category'       => $l['category']                ?? '',
                        'temperature'    => $temp,
                        'course'         => $l['course_interested']       ?? '',
                        'availability'   => $l['availability_date']       ?? '',
                        'warm_case'      => $l['warm_case']               ?? '',
                        'warm_level'     => $l['warm_level']              ?? '',
                        'next_follow_up' => $l['next_follow_up']          ?? '',
                        'cold_reason'    => $l['cold_reason']             ?? '',
                        'no_pursue'      => $l['no_pursue_reason']        ?? '',
                        'nc_reason'      => $l['not_communicated_reason'] ?? '',
                        'call_duration'  => $l['call_duration']           ?? '',
                        'call_type'      => $l['call_type']               ?? 'Fresh Call',
                    ]);
                ?>
                <tr>
                    <td class="text-muted" style="font-size:11px;"><?= ($result['page']-1)*50+$i+1 ?></td>
                    <td>
                        <div class="fw-semibold"><?= e($l['student_name']) ?></div>
                        <?php if ($l['father_name']): ?><div class="text-muted" style="font-size:11px;">F: <?= e($l['father_name']) ?></div><?php endif; ?>
                    </td>
                    <td>
                        <a href="tel:<?= e($l['student_contact']) ?>" class="tc-call-link"><i class="fas fa-phone me-1"></i><?= e($l['student_contact']) ?></a>
                        <?php if ($l['parent_contact']): ?><div class="text-muted" style="font-size:11px;"><i class="fas fa-user me-1"></i><?= e($l['parent_contact']) ?></div><?php endif; ?>
                    </td>
                    <td>
                        <div style="font-size:12px;"><?= e($l['school_name']) ?: '—' ?></div>
                        <div class="text-muted" style="font-size:11px;"><?= e($l['district']) ?><?= $l['village'] ? ' · ' . e($l['village']) : '' ?></div>
                    </td>
                    <td><?= $tempBadge ?></td>
                    <td style="font-size:12px;"><?= e($l['course_interested']) ?: '—' ?></td>
                    <td style="font-size:12px;"><?= e($l['warm_level']) ?: '—' ?></td>
                    <td><span class="<?= $fuCls ?>" style="font-size:12px;"><?= $fuText ?></span></td>
                    <td style="text-align:center;">
                        <a href="<?= BASE_URL ?>/tc/leads/<?= $l['id'] ?>/edit" class="btn btn-sm tc-update-btn">
                            <i class="fas fa-pen me-1"></i>Update
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($result['pages'] > 1): ?>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">Page <?= $result['page'] ?> of <?= $result['pages'] ?> — <?= number_format($result['total']) ?> leads</small>
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

<!-- ════════════════════════════ TABBED MODAL ════════════════════════════ -->
<div class="modal fade" id="leadEditModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content tc-modal">

            <!-- Header -->
            <div class="tc-modal-header">
                <div>
                    <h5 class="mb-0 text-white"><i class="fas fa-user-edit me-2" style="color:#38bdf8;"></i>Update Lead</h5>
                    <small id="modalLeadName" style="color:#94a3b8;font-size:12px;"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Tabs -->
            <div class="tc-modal-tabs">
                <button class="tc-tab active" data-target="tabStudentInfo">
                    <i class="fas fa-user me-2"></i>Student Info
                </button>
                <button class="tc-tab" data-target="tabLeadStatus">
                    <i class="fas fa-chart-line me-2"></i>Lead Status
                </button>
            </div>

            <input type="hidden" id="editLeadId">

            <!-- ─── TAB 1: Student Info ─── -->
            <div id="tabStudentInfo" class="tc-tab-panel active">
                <div class="tc-section-label">
                    <i class="fas fa-exclamation-triangle me-2" style="color:#f59e0b;"></i>
                    Correct any wrong student information below
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="tc-label">Student Name</label>
                        <input type="text" id="eStudentName" class="form-control tc-input" placeholder="Full student name">
                    </div>
                    <div class="col-md-6">
                        <label class="tc-label">Father's Name</label>
                        <input type="text" id="eFatherName" class="form-control tc-input" placeholder="Father's full name">
                    </div>
                    <div class="col-md-6">
                        <label class="tc-label"><i class="fas fa-phone me-1 text-success"></i>Student Contact</label>
                        <input type="text" id="eSContact" class="form-control tc-input" placeholder="10-digit mobile number" maxlength="15">
                    </div>
                    <div class="col-md-6">
                        <label class="tc-label"><i class="fas fa-phone me-1 text-primary"></i>Parent Contact</label>
                        <input type="text" id="ePContact" class="form-control tc-input" placeholder="10-digit mobile number" maxlength="15">
                    </div>
                    <div class="col-md-12">
                        <label class="tc-label"><i class="fas fa-school me-1" style="color:#8b5cf6;"></i>School Name</label>
                        <input type="text" id="eSchool" class="form-control tc-input" placeholder="School / College name">
                    </div>
                    <div class="col-md-4">
                        <label class="tc-label">District</label>
                        <input type="text" id="eDistrict" class="form-control tc-input" placeholder="District">
                    </div>
                    <div class="col-md-4">
                        <label class="tc-label">Village</label>
                        <input type="text" id="eVillage" class="form-control tc-input" placeholder="Village / City">
                    </div>
                    <div class="col-md-4">
                        <label class="tc-label">Stream</label>
                        <input type="text" id="eStream" class="form-control tc-input" placeholder="e.g. Science, Commerce">
                    </div>
                    <div class="col-md-6">
                        <label class="tc-label">Category</label>
                        <input type="text" id="eCategory" class="form-control tc-input" placeholder="e.g. General, OBC, SC/ST">
                    </div>
                </div>
            </div>

            <!-- ─── TAB 2: Lead Status ─── -->
            <div id="tabLeadStatus" class="tc-tab-panel">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="tc-label">Status</label>
                        <select id="eTemp" class="form-select tc-input">
                            <option value="HOT">🔥 HOT</option>
                            <option value="Warm">🌡️ Warm</option>
                            <option value="Cold">❄️ Cold</option>
                            <option value="Not Communicated">📵 Not Communicated</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="tc-label">Course (Interested)</label>
                        <input type="text" id="eCourse" class="form-control tc-input" placeholder="e.g. B.Tech, MBA…">
                    </div>
                    <div class="col-md-4">
                        <label class="tc-label">Date of Availability</label>
                        <input type="date" id="eAvailability" class="form-control tc-input">
                    </div>
                </div>

                <!-- Warm section -->
                <div id="warmSection" class="tc-cond-section tc-warm">
                    <div class="tc-cond-label"><i class="fas fa-temperature-high me-2"></i>In case of Warm</div>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="tc-label">Warm Situation</label>
                            <select id="eWarmCase" class="form-select tc-input">
                                <option value="">— Select —</option>
                                <option value="Not Decided Yet">Not Decided Yet</option>
                                <option value="Discuss with Family">Discuss with Family</option>
                                <option value="Communicated with Parents (Student Not Available)">Communicated with Parents (Student Not Available)</option>
                                <option value="Follow up Required">Follow up Required</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="tc-label">Warm Level</label>
                            <select id="eWarmLevel" class="form-select tc-input">
                                <option value="">— Level —</option>
                                <option value="Level-1">Level-1</option>
                                <option value="Level-2">Level-2</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Follow-up date (HOT / Warm) -->
                <div id="followupSection" class="tc-cond-section tc-hot">
                    <div class="tc-cond-label"><i class="fas fa-calendar-check me-2"></i>Next Date of Follow Up <small>(Hot / Warm)</small></div>
                    <input type="date" id="eFollowup" class="form-control tc-input" style="max-width:240px;">
                </div>

                <!-- Not Communicated section -->
                <div id="ncSection" class="tc-cond-section tc-nc">
                    <div class="tc-cond-label"><i class="fas fa-phone-slash me-2"></i>If Not Communicated — Reason</div>
                    <select id="eNcReason" class="form-select tc-input">
                        <option value="">— Select reason —</option>
                        <option value="Phone Switch Off">Phone Switch Off</option>
                        <option value="Call Not Connected">Call Not Connected</option>
                        <option value="Call Not Recieved">Call Not Recieved</option>
                        <option value="Invalid Number">Invalid Number</option>
                        <option value="incomming off">Incoming Off</option>
                        <option value="number busy">Number Busy</option>
                        <option value="out of services">Out of Services</option>
                    </select>
                </div>

                <!-- Call Info (always visible) -->
                <div class="row g-3 mt-1" style="background:#f1f5f9;border-radius:12px;padding:14px 10px;">
                    <div class="col-md-5">
                        <label class="tc-label"><i class="fas fa-clock me-1"></i>Duration of Call <small>(minutes)</small></label>
                        <input type="number" id="eCallDuration" class="form-control tc-input" placeholder="e.g. 2.5" step="0.1" min="0">
                    </div>
                    <div class="col-md-7">
                        <label class="tc-label"><i class="fas fa-redo me-1"></i>Fresh / Follow up Call</label>
                        <select id="eCallType" class="form-select tc-input">
                            <option value="Fresh Call">📞 Fresh Call</option>
                            <option value="Follow up call">🔄 Follow up call</option>
                        </select>
                    </div>
                </div>
                <div id="coldSection" class="tc-cond-section tc-cold">
                    <div class="tc-cond-label"><i class="fas fa-snowflake me-2"></i>If Cold — Reason</div>
                    <select id="eColdReason" class="form-select tc-input mb-2">
                        <option value="">— Select reason —</option>
                        <option value="Admission Taken in Other College">Admission Taken in Other College</option>
                        <option value="Don't Want to Pursue Further Study">Don't Want to Pursue Further Study</option>
                        <option value="Call Disconnected Immediately">Call Disconnected Immediately</option>
                        <option value="Communicated Very Rudely">Communicated Very Rudely</option>
                        <option value="WRONG NO.">WRONG NO.</option>
                        <option value="Course not available">Course not available</option>
                        <option value="Student now in 11th class">Student now in 11th class</option>
                    </select>
                    <div id="noPursueGroup" style="display:none;">
                        <label class="tc-label">Reason if Don't Want to Pursue Further Study</label>
                        <input type="text" id="eNoPursue" class="form-control tc-input" placeholder="Explain reason…">
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="tc-modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveLeadBtn" class="btn btn-sm tc-save-btn">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Toast -->
<div id="tcToast" style="display:none;position:fixed;bottom:24px;right:24px;z-index:99999;padding:12px 22px;border-radius:12px;font-size:13px;font-weight:700;color:#fff;box-shadow:0 4px 24px rgba(0,0,0,.18);opacity:1;transition:opacity .3s;"></div>

<style>
/* ── Badges ── */
.tc-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-weight:700; font-size:11px; }
.tc-badge-hot  { background:#fef2f2; color:#b91c1c; }
.tc-badge-warm { background:#fefce8; color:#92400e; }
.tc-badge-cold { background:#eff6ff; color:#1d4ed8; }
.tc-badge-nc   { background:#f1f5f9; color:#475569; }

/* ── Update Button ── */
.tc-update-btn {
    background: linear-gradient(135deg,#0ea5e9,#6366f1);
    color:#fff; border:none; border-radius:8px;
    font-weight:600; font-size:12px; padding:5px 14px;
    transition: transform .15s, box-shadow .15s;
}
.tc-update-btn:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(99,102,241,.35); color:#fff; }

/* ── Modal ── */
.tc-modal { border:none; border-radius:16px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.25); }
.tc-modal-header {
    background: linear-gradient(135deg,#0f172a,#1e293b);
    padding: 18px 24px; display:flex; justify-content:space-between; align-items:center;
}
.tc-modal-tabs {
    display:flex; background:#f1f5f9; border-bottom:2px solid #e2e8f0; padding:0 24px;
}
.tc-tab {
    padding:12px 20px; border:none; background:transparent; font-size:13px;
    font-weight:600; color:#64748b; cursor:pointer; border-bottom:3px solid transparent;
    margin-bottom:-2px; transition:all .2s;
}
.tc-tab.active { color:#6366f1; border-bottom-color:#6366f1; background:#fff; }
.tc-tab:hover:not(.active) { color:#334155; background:#e8edf3; }

.tc-tab-panel { display:none; padding:24px; background:#f8fafc; }
.tc-tab-panel.active { display:block; }

.tc-label { font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.4px; margin-bottom:4px; display:block; }
.tc-input { border-radius:10px; font-size:13px; border:1.5px solid #e2e8f0; }
.tc-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); }

.tc-section-label {
    background:#fef3c7; border:1px solid #fcd34d; border-radius:10px;
    padding:10px 14px; font-size:12px; font-weight:600; color:#92400e;
    margin-bottom:18px;
}
.tc-cond-section { border-radius:12px; padding:16px; margin-bottom:14px; display:none; }
.tc-cond-section.show { display:block; }
.tc-cond-label { font-size:12px; font-weight:700; margin-bottom:12px; }
.tc-warm  { background:#fefce8; border:1.5px solid #fde68a; }
.tc-warm .tc-cond-label  { color:#92400e; }
.tc-hot   { background:#f0fdf4; border:1.5px solid #bbf7d0; }
.tc-hot .tc-cond-label   { color:#15803d; }
.tc-cold  { background:#eff6ff; border:1.5px solid #bfdbfe; }
.tc-cold .tc-cond-label  { color:#1d4ed8; }
.tc-nc    { background:#f8fafc; border:1.5px solid #cbd5e1; }
.tc-nc .tc-cond-label    { color:#475569; }

.tc-modal-footer {
    background:#f8fafc; border-top:1px solid #e2e8f0;
    padding:14px 24px; display:flex; justify-content:flex-end; gap:10px;
}
.tc-save-btn {
    background:linear-gradient(135deg,#0ea5e9,#6366f1);
    color:#fff; font-weight:700; border-radius:10px; padding:8px 26px; border:none;
}
.tc-save-btn:hover { opacity:.9; color:#fff; }
.tc-save-btn:disabled { opacity:.6; }
</style>

<script>
const BASE = '<?= BASE_URL ?>';

/* ── Toast ── */
function toast(msg, ok = true) {
    const el = document.getElementById('tcToast');
    el.style.background = ok ? '#10b981' : '#ef4444';
    el.innerHTML = (ok ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="fas fa-times-circle me-2"></i>') + msg;
    el.style.display = 'block'; el.style.opacity = '1';
    setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.style.display = 'none', 350); }, 2600);
}

/* ── Patch a single field ── */
function patch(leadId, field, value) {
    const fd = new FormData();
    fd.append('field', field); fd.append('value', value);
    return fetch(BASE + '/tc/leads/' + leadId + '/quick-update', { method:'POST', body:fd })
           .then(r => r.json());
}

/* ── Tab switching ── */
document.querySelectorAll('.tc-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.tc-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tc-tab-panel').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(this.dataset.target).classList.add('active');
    });
});

/* ── Status → conditional sections ── */
function toggleSections(status) {
    document.getElementById('warmSection').classList.toggle('show',    status === 'Warm');
    document.getElementById('followupSection').classList.toggle('show', status === 'HOT' || status === 'Warm');
    document.getElementById('coldSection').classList.toggle('show',     status === 'Cold');
    document.getElementById('ncSection').classList.toggle('show',       status === 'Not Communicated');
}
document.getElementById('eTemp').addEventListener('change', function() { toggleSections(this.value); });
document.getElementById('eColdReason').addEventListener('change', function() {
    document.getElementById('noPursueGroup').style.display =
        this.value === "Don't Want to Pursue Further Study" ? 'block' : 'none';
});

/* ── Open modal ── */
function openModal(d) {
    document.getElementById('editLeadId').value       = d.id;
    document.getElementById('modalLeadName').textContent = d.name;

    // Student info tab
    document.getElementById('eStudentName').value = d.name       || '';
    document.getElementById('eFatherName').value  = d.father_name || '';
    document.getElementById('eSContact').value    = d.s_contact  || '';
    document.getElementById('ePContact').value    = d.p_contact  || '';
    document.getElementById('eSchool').value      = d.school     || '';
    document.getElementById('eDistrict').value    = d.district   || '';
    document.getElementById('eVillage').value     = d.village    || '';
    document.getElementById('eStream').value      = d.stream     || '';
    document.getElementById('eCategory').value    = d.category   || '';

    // Lead status tab
    document.getElementById('eTemp').value         = d.temperature    || 'Cold';
    document.getElementById('eCourse').value       = d.course         || '';
    document.getElementById('eAvailability').value = d.availability   || '';
    document.getElementById('eWarmCase').value     = d.warm_case      || '';
    document.getElementById('eWarmLevel').value    = d.warm_level     || '';
    document.getElementById('eFollowup').value     = d.next_follow_up || '';
    document.getElementById('eColdReason').value   = d.cold_reason    || '';
    document.getElementById('eNoPursue').value     = d.no_pursue      || '';
    document.getElementById('eNcReason').value     = d.nc_reason      || '';
    document.getElementById('eCallDuration').value = d.call_duration  || '';
    document.getElementById('eCallType').value     = d.call_type      || 'Fresh Call';

    toggleSections(d.temperature);
    document.getElementById('noPursueGroup').style.display =
        d.cold_reason === "Don't Want to Pursue Further Study" ? 'block' : 'none';

    // Reset to first tab
    document.querySelectorAll('.tc-tab').forEach((t,i)   => t.classList.toggle('active', i===0));
    document.querySelectorAll('.tc-tab-panel').forEach((p,i) => p.classList.toggle('active', i===0));

    new bootstrap.Modal(document.getElementById('leadEditModal')).show();
}

/* ── Save all fields ── */
document.getElementById('saveLeadBtn').addEventListener('click', async function() {
    const lid = document.getElementById('editLeadId').value;
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving…';

    const fields = {
        // Student info
        student_name     : document.getElementById('eStudentName').value,
        father_name      : document.getElementById('eFatherName').value,
        student_contact  : document.getElementById('eSContact').value,
        parent_contact   : document.getElementById('ePContact').value,
        school_name      : document.getElementById('eSchool').value,
        district         : document.getElementById('eDistrict').value,
        village          : document.getElementById('eVillage').value,
        stream           : document.getElementById('eStream').value,
        category         : document.getElementById('eCategory').value,
        // Lead status
        temperature      : document.getElementById('eTemp').value,
        course_interested: document.getElementById('eCourse').value,
        availability_date: document.getElementById('eAvailability').value,
        warm_case        : document.getElementById('eWarmCase').value,
        warm_level       : document.getElementById('eWarmLevel').value,
        next_follow_up   : document.getElementById('eFollowup').value,
        cold_reason      : document.getElementById('eColdReason').value,
        no_pursue_reason : document.getElementById('eNoPursue').value,
        // Call info
        not_communicated_reason: document.getElementById('eNcReason').value,
        call_duration    : document.getElementById('eCallDuration').value,
        call_type        : document.getElementById('eCallType').value,
    };

    try {
        const results = await Promise.all(
            Object.entries(fields).map(([f, v]) => patch(lid, f, v))
        );
        const allOk = results.every(r => r.ok);
        if (allOk) {
            toast('Lead updated successfully! ✅', true);
            bootstrap.Modal.getInstance(document.getElementById('leadEditModal')).hide();
            setTimeout(() => location.reload(), 950);
        } else {
            toast('Some fields could not be saved.', false);
        }
    } catch(e) {
        toast('Save failed — check connection and try again.', false);
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save me-2"></i>Save Changes';
});
</script>
