<?php
// Admin — Daily Calling Report
$grandHot  = array_sum(array_column($rows, 'hot_calls'));
$grandWarm = array_sum(array_column($rows, 'warm_calls'));
$grandCold = array_sum(array_column($rows, 'cold_calls'));
$grandNC   = array_sum(array_column($rows, 'nc_calls'));
$grandAdm  = array_sum(array_column($rows, 'admissions'));
$grandTotal= array_sum(array_column($rows, 'total_calls'));
$grandEff  = array_sum(array_column($rows, 'effective_calls'));
$grandMins = array_sum(array_column($rows, 'total_minutes'));
$effMins   = 0; // effective comm mins placeholder (call_duration tracked separately)
?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-phone-volume me-2" style="color:#10b981;"></i>Daily Calling Report</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Daily Calling Report</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <a href="<?= BASE_URL ?>/reports/hot-warm?date=<?= e($date) ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-fire me-1"></i>HOT &amp; WARM Report
        </a>
        <button onclick="window.print()" class="btn btn-sm" style="background:#1e293b;color:#fff;border-radius:8px;">
            <i class="fas fa-print me-1"></i>Print / Export
        </button>
    </div>
</div>

<!-- Date Picker -->
<div class="card dashboard-card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="" class="d-flex align-items-end gap-3">
            <input type="hidden" name="route" value="reports/daily">
            <div>
                <label class="form-label fw-semibold mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.4px;color:#64748b;">Please Select Date</label>
                <input type="date" name="date" value="<?= e($date) ?>" class="form-control" style="border-radius:10px;font-size:13px;min-width:180px;">
            </div>
            <button class="btn btn-primary" style="background:linear-gradient(135deg,#10b981,#0ea5e9);border:none;border-radius:10px;">
                <i class="fas fa-search me-1"></i>View Report
            </button>
        </form>
    </div>
</div>

<!-- Printable Report -->
<div id="reportArea">
    <div class="report-header text-center mb-3">
        <h5 class="report-title">AKAL UNIVERSITY, TALWANDI SABO</h5>
        <div class="report-subtitle">Daily Calling Report</div>
        <div class="report-date">Date: <strong><?= date('m/d/Y', strtotime($date)) ?></strong></div>
    </div>

    <div class="card dashboard-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table report-table mb-0">
                    <thead>
                        <tr>
                            <th rowspan="2">Name of Tele Caller</th>
                            <th rowspan="2">Date of Calling</th>
                            <th class="text-center text-danger">No. of Hot</th>
                            <th class="text-center" style="color:#d97706;">No. of Warm</th>
                            <th class="text-center text-primary">No. of Cold</th>
                            <th class="text-center text-secondary">No. of Not Communicated</th>
                            <th class="text-center text-success">No. of Admissions Done</th>
                            <th class="text-center fw-bold">Total Calls</th>
                            <th class="text-center">No. of Effective Calls</th>
                            <th class="text-center">Time in Minutes</th>
                            <th class="text-center">Effective Communication Time (In Minutes)</th>
                            <th class="text-center">Effective Communication Time (Hours)</th>
                        </tr>
                        <tr style="background:#334155;">
                            <th class="text-center text-danger">calls</th>
                            <th class="text-center" style="color:#fcd34d;">calls</th>
                            <th class="text-center" style="color:#93c5fd;">calls</th>
                            <th class="text-center" style="color:#cbd5e1;">Calls</th>
                            <th class="text-center" style="color:#6ee7b7;"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($rows)): ?>
                    <tr><td colspan="12" class="text-center py-5 text-muted">
                        <i class="fas fa-inbox me-2"></i>No calling data found for <?= date('d M Y', strtotime($date)) ?>
                    </td></tr>
                    <?php else: ?>
                    <?php foreach ($rows as $r):
                        $mins   = (float)$r['total_minutes'];
                        $hours  = $mins > 0 ? sprintf('%02d:%02d', floor($mins / 60), round(fmod($mins, 60))) : '00:00';
                        // effective minutes = (effective calls / total calls) * total_minutes
                        $effM   = $r['total_calls'] > 0 ? round(($r['effective_calls'] / $r['total_calls']) * $mins) : 0;
                    ?>
                    <tr>
                        <td class="fw-semibold"><?= e(strtoupper($r['caller_name'])) ?></td>
                        <td><?= date('m/d/Y', strtotime($r['call_date'])) ?></td>
                        <td class="text-center <?= $r['hot_calls']  > 0 ? 'text-danger fw-bold' : 'text-muted' ?>"><?= $r['hot_calls']  ?></td>
                        <td class="text-center <?= $r['warm_calls'] > 0 ? 'fw-semibold' : 'text-muted' ?>" style="<?= $r['warm_calls']>0?'color:#d97706;':'' ?>"><?= $r['warm_calls'] ?></td>
                        <td class="text-center text-primary"><?= $r['cold_calls'] ?></td>
                        <td class="text-center text-secondary"><?= $r['nc_calls'] ?></td>
                        <td class="text-center text-success fw-semibold"><?= $r['admissions'] ?></td>
                        <td class="text-center fw-bold"><?= $r['total_calls'] ?></td>
                        <td class="text-center"><?= $r['effective_calls'] ?></td>
                        <td class="text-center"><?= number_format($mins, 2) ?></td>
                        <td class="text-center"><?= $effM ?></td>
                        <td class="text-center"><?= $hours ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <!-- spacer rows -->
                    <?php for ($i = 0; $i < max(0, 3 - count($rows)); $i++): ?>
                    <tr><td colspan="12" style="height:32px;"></td></tr>
                    <?php endfor; ?>
                    <?php endif; ?>
                    </tbody>
                    <?php if (!empty($rows)): ?>
                    <tfoot>
                        <tr class="grand-total-row">
                            <td colspan="2" class="fw-bold" style="font-size:13px;">Grand Total</td>
                            <td class="text-center fw-bold text-danger"><?= $grandHot ?></td>
                            <td class="text-center fw-bold" style="color:#d97706;"><?= $grandWarm ?></td>
                            <td class="text-center fw-bold text-primary"><?= $grandCold ?></td>
                            <td class="text-center fw-bold text-secondary"><?= $grandNC ?></td>
                            <td class="text-center fw-bold text-success"><?= $grandAdm ?></td>
                            <td class="text-center fw-bold"><?= $grandTotal ?></td>
                            <td class="text-center fw-bold"><?= $grandEff ?></td>
                            <td class="text-center fw-bold"><?= number_format($grandMins, 2) ?></td>
                            <td class="text-center fw-bold"><?= array_sum(array_column($rows,'effective_calls')) > 0 ? round((array_sum(array_column($rows,'effective_calls')) / max($grandTotal,1)) * $grandMins) : 0 ?></td>
                            <td class="text-center fw-bold">0</td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.report-header { padding: 16px 0 8px; }
.report-title  { font-size: 20px; font-weight: 800; color: #1e293b; margin:0; }
.report-subtitle { font-size: 14px; font-weight: 700; color: #10b981; margin: 4px 0; }
.report-date   { font-size: 13px; color: #64748b; }

.report-table  { font-size: 12px; }
.report-table thead th {
    background: #1e293b; color: #e2e8f0 !important;
    font-size: 11px; font-weight: 700; letter-spacing: .3px;
    padding: 8px 8px; white-space: nowrap; border-color: #334155;
}
.report-table tbody tr:hover { background: #f0fdf4; }
.report-table tbody td { padding: 8px 8px; border-color: #e9ecef; vertical-align: middle; font-size: 12.5px; }

.grand-total-row { background: #fef9c3; border-top: 2px solid #fde68a; }
.grand-total-row td { padding: 10px 8px; font-size: 13px; }

@media print {
    .page-header, .card.dashboard-card:first-of-type,
    .sidebar, .top-navbar, .sidebar-overlay { display:none !important; }
    #reportArea { margin: 0; }
    .report-table thead th {
        background: #1e293b !important; color: #fff !important;
        -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }
    .grand-total-row { background: #fef9c3 !important; }
}
</style>
