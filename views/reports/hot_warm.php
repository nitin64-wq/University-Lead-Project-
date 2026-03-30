<?php
// Admin — DateWise HOT & WARM QUERIES Report
?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-fire me-2" style="color:#ef4444;"></i>DateWise HOT &amp; WARM Queries</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">HOT &amp; WARM Report</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <a href="<?= BASE_URL ?>/reports/daily?date=<?= e($date) ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-table me-1"></i>Daily Calling Report
        </a>
        <button onclick="printReport()" class="btn btn-sm" style="background:#1e293b;color:#fff;border-radius:8px;">
            <i class="fas fa-print me-1"></i>Print / Export
        </button>
    </div>
</div>

<!-- Date Picker -->
<div class="card dashboard-card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="" class="d-flex align-items-end gap-3">
            <input type="hidden" name="route" value="reports/hot-warm">
            <div>
                <label class="form-label fw-semibold mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.4px;color:#64748b;">Please Select Date</label>
                <input type="date" name="date" value="<?= e($date) ?>" class="form-control" style="border-radius:10px;font-size:13px;min-width:180px;">
            </div>
            <button class="btn btn-primary" style="background:linear-gradient(135deg,#0ea5e9,#6366f1);border:none;border-radius:10px;">
                <i class="fas fa-search me-1"></i>View Report
            </button>
        </form>
    </div>
</div>

<!-- Printable Report -->
<div id="reportArea">
    <div class="report-header text-center mb-3">
        <h5 class="report-title">Akal University, Talwandi Sabo</h5>
        <div class="report-subtitle">DateWise HOT &amp; WARM QUERIES</div>
        <div class="report-date">Date: <strong><?= date('m/d/Y', strtotime($date)) ?></strong></div>
    </div>

    <div class="card dashboard-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table report-table mb-0">
                    <thead>
                        <tr>
                            <th>Date of Calling</th>
                            <th>Caller Name</th>
                            <th>Name</th>
                            <th>Father Name</th>
                            <th>Contact Number</th>
                            <th>Category</th>
                            <th>Village / School Name</th>
                            <th>Village</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($leads)): ?>
                    <tr><td colspan="10" class="text-center py-5 text-muted">
                        <i class="fas fa-inbox me-2"></i>No HOT or Warm leads found for <?= date('d M Y', strtotime($date)) ?>
                    </td></tr>
                    <?php else: ?>
                    <?php foreach ($leads as $r): 
                        $hasLead = !empty($r['id']);
                        $callDate = $r['last_call_date'] ?? $date;
                    ?>
                    <tr>
                        <td><?= date('m/d/Y', strtotime($callDate)) ?></td>
                        <td class="fw-semibold"><?= e(strtoupper($r['caller_name'] ?? '')) ?></td>
                        <td><?= e(strtoupper($r['student_name'] ?? '')) ?></td>
                        <td><?= e(strtoupper($r['father_name'] ?? '')) ?></td>
                        <td><?= e($r['student_contact'] ?? '') ?></td>
                        <td><?= e(strtoupper($r['category'] ?? '')) ?></td>
                        <td><?= e($r['school_name'] ?? '') ?></td>
                        <td><?= e($r['village'] ?? '') ?></td>
                        <td>
                            <?php if ($hasLead): ?>
                                <?php
                                $cls = strtoupper($r['temperature'] ?? '') === 'HOT' ? 'text-danger fw-bold' : 'text-warning fw-bold';
                                echo "<span class='$cls'>" . e($r['temperature']) . "</span>";
                                ?>
                            <?php else: ?>
                                <span class="text-muted" style="font-size:11px;">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($r['warm_level'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!empty($leads)): 
            $totalLeads = count(array_filter($leads, fn($x) => !empty($x['id'])));
        ?>
        <div class="card-footer bg-white" style="font-size:13px;color:#64748b;">
            <i class="fas fa-list-ol me-1"></i>Total HOT &amp; Warm Leads: <strong><?= $totalLeads ?></strong>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.report-header { padding: 16px 0 8px; }
.report-title  { font-size: 20px; font-weight: 800; color: #1e293b; margin:0; }
.report-subtitle { font-size: 14px; font-weight: 700; color: #6366f1; margin: 4px 0; }
.report-date   { font-size: 13px; color: #64748b; }
.report-table  { font-size: 12.5px; }
.report-table thead th {
    background: #1e293b; color: #e2e8f0; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px; padding: 10px 10px;
    white-space: nowrap;
}
.report-table tbody tr:hover { background: #f0f9ff; }
.report-table tbody td { padding: 8px 10px; border-color: #e9ecef; vertical-align: middle; }

@media print {
    .page-header, .card.dashboard-card:first-of-type, .sidebar, .top-navbar { display:none !important; }
    #reportArea { margin: 0; }
    .report-table thead th { background: #1e293b !important; color: #fff !important; -webkit-print-color-adjust: exact; }
}
</style>

<script>
function printReport() {
    window.print();
}
</script>
