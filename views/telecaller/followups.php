<?php
// Telecaller — Follow-up Management
$todayCount    = (int)($counts['today']    ?? 0);
$upcomingCount = (int)($counts['upcoming'] ?? 0);
$overdueCount  = (int)($counts['overdue']  ?? 0);
$tabs = [
    'today'    => ['label'=>"Today's Follow-ups", 'icon'=>'bell',                 'count'=>$todayCount,    'color'=>'warning'],
    'upcoming' => ['label'=>'Upcoming Follow-ups', 'icon'=>'clock',               'count'=>$upcomingCount, 'color'=>'success'],
    'overdue'  => ['label'=>'Overdue Follow-ups',  'icon'=>'exclamation-triangle','count'=>$overdueCount,  'color'=>'danger'],
];
$validTab = in_array($tab, ['today','upcoming','overdue']) ? $tab : 'today';
$current  = $tabs[$validTab];
?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-calendar-check me-2" style="color:var(--teal);"></i>Follow-up Management</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tc/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Follow-ups</li>
        </ol></nav>
    </div>
</div>

<!-- Tab Cards -->
<div class="row g-3 mb-4">
    <?php foreach ($tabs as $key => $t): ?>
    <div class="col-md-4">
        <a href="?route=tc/followups&tab=<?= $key ?>" class="followup-tab-card <?= $tab===$key?'active':'' ?> followup-tab-<?= $t['color'] ?>">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="followup-tab-label"><i class="fas fa-<?= $t['icon'] ?> me-2"></i><?= $t['label'] ?></div>
                    <div class="followup-tab-count"><?= $t['count'] ?></div>
                </div>
                <i class="fas fa-<?= $t['icon'] ?> followup-tab-icon"></i>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- Follow-up List -->
<div class="card dashboard-card">
    <div class="card-header">
        <h6>
            <i class="fas fa-<?= $tabs[$validTab]['icon'] ?> me-2" style="color:var(--<?= $tabs[$validTab]['color']==='warning'?'warning':($tabs[$validTab]['color']==='success'?'success':'danger') ?>);"></i>
            <?= $current['label'] ?> (<?= $result['total'] ?>)
        </h6>
    </div>
    <div class="card-body p-0">
        <?php if (empty($result['data'])): ?>
        <div class="empty-state py-5">
            <i class="fas fa-calendar-check" style="color:var(--success);"></i>
            <h6><?= $tab==='today' ? 'All done for today! 🎉' : ($tab==='upcoming'?'No upcoming follow-ups':'No overdue follow-ups') ?></h6>
            <p><?= $tab==='today' ? "You're all caught up!" : 'Great job staying on track!' ?></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Contact</th>
                        <th>Course</th>
                        <th>Temp</th>
                        <th>Last Status</th>
                        <th>Follow-up Date</th>
                        <th>Last Call</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($result['data'] as $l): ?>
                <?php
                $fu = $l['next_follow_up'] ? strtotime($l['next_follow_up']) : null;
                $diff = $fu ? ($fu - strtotime('today')) : null;
                $urgency = '';
                if ($diff !== null) {
                    if ($diff < 0) $urgency = 'table-danger';
                    elseif ($diff === 0) $urgency = 'table-warning';
                }
                ?>
                <tr class="<?= $urgency ?>">
                    <td>
                        <strong style="font-size:13px;"><?= e($l['student_name']) ?></strong>
                        <div class="text-muted" style="font-size:11px;"><?= e($l['school_name']) ?></div>
                    </td>
                    <td>
                        <a href="tel:<?= e($l['student_contact']) ?>" class="tc-call-link"><i class="fas fa-phone me-1"></i><?= e($l['student_contact']) ?></a>
                    </td>
                    <td style="font-size:13px;"><?= e($l['course_interested']) ?: '—' ?></td>
                    <td><span class="badge-<?= strtolower($l['temperature']) ?>"><?= e($l['temperature']) ?></span></td>
                    <td><span class="badge-status badge-<?= strtolower(str_replace([' ','/'],'',$l['lead_status'])) ?>"><?= e($l['lead_status']) ?></span></td>
                    <td style="font-size:13px;font-weight:600;">
                        <?php if ($fu): ?>
                            <?= date('d M Y', $fu) ?>
                            <?php if ($diff < 0): ?><div class="text-danger" style="font-size:11px;"><?= abs((int)round($diff/86400)) ?> days overdue</div>
                            <?php elseif ($diff === 0): ?><div class="text-warning" style="font-size:11px;">Today!</div>
                            <?php else: ?><div class="text-success" style="font-size:11px;">in <?= round($diff/86400) ?> days</div><?php endif; ?>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <td style="font-size:12px;" class="text-muted">
                        <?= $l['last_call_date'] ? date('d M Y', strtotime($l['last_call_date'])) : '—' ?>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>/tc/leads/<?= $l['id'] ?>" class="btn btn-sm" style="background:#e0f2f1;color:#0d9488;border-radius:8px;">
                            <i class="fas fa-phone-alt me-1"></i>Log Call
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
