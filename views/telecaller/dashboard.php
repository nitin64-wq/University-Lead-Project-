<?php
// Telecaller Dashboard
$today = date('D, d M Y');
?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-th-large me-2" style="color:var(--teal);"></i>My Dashboard</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active"><?= e($today) ?></li></ol></nav>
    </div>
    <a href="<?= BASE_URL ?>/tc/leads" class="btn btn-sm" style="background:var(--teal);color:#fff;border-radius:10px;">
        <i class="fas fa-users me-1"></i>View My Leads
    </a>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['primary', 'users',              'Assigned Leads',   $stats['total']         ?? 0, 'Total leads assigned to me'],
        ['danger',  'fire',               'Hot Leads',        $stats['hot']           ?? 0, 'High priority'],
        ['warning', 'temperature-half',   'Warm Leads',       $stats['warm']          ?? 0, 'Needs nurturing'],
        ['info',    'snowflake',          'Cold Leads',       $stats['cold']          ?? 0, 'Low engagement'],
        ['orange',  'calendar-day',       "Today's Follow-ups",$stats['today_followup']?? 0, 'Call these today'],
        ['success', 'graduation-cap',     'Admissions Done',  $stats['admitted']      ?? 0, 'Converted leads'],
    ];
    foreach ($cards as [$color,$icon,$label,$val,$sub]): ?>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-card-<?= $color ?>">
            <div class="stat-card-body">
                <div class="stat-card-icon"><i class="fas fa-<?= $icon ?>"></i></div>
                <div><h3><?= number_format((int)$val) ?></h3><p><?= $label ?></p></div>
            </div>
            <div class="stat-card-footer"><i class="fas fa-info-circle me-1"></i><?= $sub ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Row: Follow-up status + Weekly Activity -->
<div class="row g-4 mb-4">
    <!-- Follow-up Summary -->
    <div class="col-xl-4">
        <div class="card dashboard-card h-100">
            <div class="card-header"><h6><i class="fas fa-calendar-check me-2" style="color:var(--teal);"></i>Follow-up Overview</h6></div>
            <div class="card-body">
                <a href="<?= BASE_URL ?>/tc/followups?tab=today" class="followup-row followup-today">
                    <div class="followup-left"><i class="fas fa-bell"></i><span>Today</span></div>
                    <span class="followup-count"><?= (int)($followups['today'] ?? 0) ?></span>
                </a>
                <a href="<?= BASE_URL ?>/tc/followups?tab=upcoming" class="followup-row followup-upcoming">
                    <div class="followup-left"><i class="fas fa-clock"></i><span>Upcoming</span></div>
                    <span class="followup-count"><?= (int)($followups['upcoming'] ?? 0) ?></span>
                </a>
                <a href="<?= BASE_URL ?>/tc/followups?tab=overdue" class="followup-row followup-overdue">
                    <div class="followup-left"><i class="fas fa-exclamation-triangle"></i><span>Overdue</span></div>
                    <span class="followup-count"><?= (int)($followups['overdue'] ?? 0) ?></span>
                </a>

                <hr class="my-3">
                <div class="row text-center g-2">
                    <div class="col-4">
                        <div style="font-size:22px;font-weight:800;color:var(--primary);"><?= (int)($callStats['total_calls'] ?? 0) ?></div>
                        <div style="font-size:11px;color:var(--text-muted);">Total Calls</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:22px;font-weight:800;color:var(--success);"><?= (int)($callStats['calls_today'] ?? 0) ?></div>
                        <div style="font-size:11px;color:var(--text-muted);">Today</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:22px;font-weight:800;color:var(--teal);"><?= (int)($callStats['calls_this_week'] ?? 0) ?></div>
                        <div style="font-size:11px;color:var(--text-muted);">This Week</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Chart -->
    <div class="col-xl-8">
        <div class="card dashboard-card h-100">
            <div class="card-header"><h6><i class="fas fa-chart-bar me-2" style="color:var(--teal);"></i>My Weekly Call Activity</h6></div>
            <div class="card-body">
                <canvas id="weeklyChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Row: Today's Follow-ups + Recent Logs -->
<div class="row g-4">
    <!-- Today's Follow-ups -->
    <div class="col-xl-6">
        <div class="card dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-calendar-day me-2" style="color:var(--orange);"></i>Today's Follow-ups</h6>
                <a href="<?= BASE_URL ?>/tc/followups?tab=today" class="btn btn-sm btn-outline-secondary">See All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($todayLeads)): ?>
                <div class="empty-state py-4"><i class="fas fa-check-circle" style="color:var(--success);"></i><h6>All done!</h6><p>No follow-ups today.</p></div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($todayLeads as $l): ?>
                    <a href="<?= BASE_URL ?>/tc/leads/<?= $l['id'] ?>" class="list-group-item list-group-item-action py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong style="font-size:14px;"><?= e($l['student_name']) ?></strong>
                                <div class="text-muted" style="font-size:12px;"><?= e($l['school_name']) ?> · <?= e($l['student_contact']) ?></div>
                            </div>
                            <span class="badge-<?= strtolower($l['temperature']) ?>"><?= e($l['temperature']) ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Call Logs -->
    <div class="col-xl-6">
        <div class="card dashboard-card">
            <div class="card-header"><h6><i class="fas fa-history me-2" style="color:var(--purple);"></i>Recent Call Logs</h6></div>
            <div class="card-body p-0">
                <?php if (empty($recentLogs)): ?>
                <div class="empty-state py-4"><i class="fas fa-phone-slash"></i><h6>No calls yet</h6><p>Your call logs will appear here.</p></div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentLogs as $log): ?>
                    <div class="list-group-item py-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong style="font-size:13px;"><?= e($log['student_name']) ?></strong>
                                <div class="text-muted" style="font-size:11px;"><?= e($log['call_type']) ?> · <?= e($log['call_duration']) ?: '—' ?></div>
                            </div>
                            <div class="text-end">
                                <span class="badge-status badge-<?= strtolower(str_replace([' ','/'],'',$log['lead_status'])) ?>"><?= e($log['lead_status']) ?></span>
                                <div class="text-muted" style="font-size:11px;"><?= date('d M, h:i A', strtotime($log['created_at'])) ?></div>
                            </div>
                        </div>
                        <?php if ($log['remarks']): ?>
                        <div class="mt-1 text-muted" style="font-size:11px;"><i class="fas fa-comment me-1"></i><?= e(substr($log['remarks'],0,80)) ?>...</div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Chart data
$weekDates = []; $weekCounts = [];
if (!empty($weekly)) {
    foreach ($weekly as $w) { $weekDates[] = date('D d', strtotime($w['call_date'])); $weekCounts[] = (int)$w['cnt']; }
}
ob_start(); ?>
<script>
(function(){
    const labels = <?= json_encode($weekDates) ?>;
    const data   = <?= json_encode($weekCounts) ?>;
    if (!labels.length) return;
    new Chart(document.getElementById('weeklyChart'), {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Calls Made', data, backgroundColor: 'rgba(13,148,136,0.7)', borderRadius: 6, hoverBackgroundColor: 'rgba(13,148,136,1)' }] },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }, responsive: true }
    });
})();
</script>
<?php $extraJs = ob_get_clean(); ?>
