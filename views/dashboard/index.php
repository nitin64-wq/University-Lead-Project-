<?php
// Helper for chart datasets
function chartJson(array $rows, string $limit = ''): string {
    if ($limit) $rows = array_slice($rows, 0, (int)$limit);
    $labels = array_column($rows, 'label');
    $data   = array_column($rows, 'cnt');
    return json_encode(['labels' => $labels, 'data' => $data]);
}
$districtChart = chartJson($byDistrict);
$schoolChart   = chartJson($bySchool, '10');
$courseChart   = chartJson($byCourse);
$statusChart   = chartJson($byStatus);
?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-th-large me-2"></i>Dashboard</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Home</a></li><li class="breadcrumb-item active">Dashboard</li></ol></nav>
    </div>
    <a href="<?= BASE_URL ?>/leads/import" class="btn btn-primary btn-sm"><i class="fas fa-file-import me-1"></i>Import Leads</a>
</div>

<!-- Stat Cards Row 1 -->
<div class="row g-4 mb-4">
    <?php
    $cards = [
        ['primary','users','Total Leads', $stats['total'] ?? 0,'All time'],
        ['danger','fire','Hot Leads',   $stats['hot']   ?? 0,'High priority'],
        ['warning','temperature-half','Warm Leads', $stats['warm'] ?? 0,'Potential'],
        ['info','snowflake','Cold Leads', $stats['cold'] ?? 0,'Need nurturing'],
    ];
    foreach ($cards as [$color,$icon,$label,$val,$sub]):
    ?>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-card-<?= $color ?>">
            <div class="stat-card-body">
                <div class="stat-card-icon"><i class="fas fa-<?= $icon ?>"></i></div>
                <div><h3><?= number_format($val) ?></h3><p><?= $label ?></p></div>
            </div>
            <div class="stat-card-footer"><i class="fas fa-info-circle me-1"></i><?= $sub ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Stat Cards Row 2 -->
<div class="row g-4 mb-4">
    <?php
    $cards2 = [
        ['success','graduation-cap','Admission Done',$stats['admitted']??0,'Converted'],
        ['purple','user-group','Total Teams', count($teams??[]),'Active teams'],
        ['teal','headset','Telecallers', count($members??[]),'Active members'],
        ['orange','clock','Pending Follow-ups',$stats['pending_followup']??0,'Needs attention'],
    ];
    foreach ($cards2 as [$color,$icon,$label,$val,$sub]):
    ?>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-card-<?= $color ?>">
            <div class="stat-card-body">
                <div class="stat-card-icon"><i class="fas fa-<?= $icon ?>"></i></div>
                <div><h3><?= number_format($val) ?></h3><p><?= $label ?></p></div>
            </div>
            <div class="stat-card-footer"><i class="fas fa-info-circle me-1"></i><?= $sub ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Charts -->
<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card dashboard-card"><div class="card-header"><h6><i class="fas fa-chart-bar me-2"></i>Leads by District</h6></div>
        <div class="card-body"><canvas id="districtChart" height="280"></canvas></div></div>
    </div>
    <div class="col-xl-4">
        <div class="card dashboard-card"><div class="card-header"><h6><i class="fas fa-chart-pie me-2"></i>Lead Status</h6></div>
        <div class="card-body"><canvas id="statusChart" height="280"></canvas></div></div>
    </div>
</div>
<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="card dashboard-card"><div class="card-header"><h6><i class="fas fa-chart-bar me-2"></i>Top 10 Schools</h6></div>
        <div class="card-body"><canvas id="schoolChart" height="280"></canvas></div></div>
    </div>
    <div class="col-xl-6">
        <div class="card dashboard-card"><div class="card-header"><h6><i class="fas fa-chart-bar me-2"></i>Leads by Course</h6></div>
        <div class="card-body"><canvas id="courseChart" height="280"></canvas></div></div>
    </div>
</div>

<!-- Recent Leads -->
<div class="card dashboard-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6><i class="fas fa-clock me-2"></i>Recent Leads</h6>
        <a href="<?= BASE_URL ?>/leads" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Student</th><th>School</th><th>District</th><th>Course</th><th>Temperature</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($recent as $l): ?>
                <tr>
                    <td><strong><?= e($l['student_name']) ?></strong></td>
                    <td><?= e($l['school_name']) ?></td>
                    <td><?= e($l['district']) ?></td>
                    <td><?= e($l['course_interested']) ?></td>
                    <td><span class="badge-<?= strtolower($l['temperature']) ?>"><?= e($l['temperature']) ?></span></td>
                    <td><span class="badge-status badge-<?= strtolower(str_replace(' ','',$l['lead_status'])) ?>"><?= e($l['lead_status']) ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$colors = ['#4f46e5','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#0d9488'];
function mkColors(int $n, array $palette): array { $out=[]; for($i=0;$i<$n;$i++) $out[]=$palette[$i%count($palette)]; return $out; }
$dc = json_decode($districtChart, true);
$sc = json_decode($schoolChart, true);
$cc = json_decode($courseChart, true);
$st = json_decode($statusChart, true);
ob_start(); ?>
<script>
(function() {
    const bar = (id, labels, data, colors) => new Chart(document.getElementById(id), {
        type:'bar', data:{labels, datasets:[{data, backgroundColor:colors, borderRadius:6}]},
        options:{plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}, responsive:true}
    });
    const doughnut = (id, labels, data, colors) => new Chart(document.getElementById(id), {
        type:'doughnut', data:{labels, datasets:[{data, backgroundColor:colors}]},
        options:{plugins:{legend:{position:'bottom'}}, responsive:true}
    });
    bar('districtChart', <?= json_encode($dc['labels']) ?>, <?= json_encode($dc['data']) ?>, <?= json_encode(mkColors(count($dc['labels']), $colors)) ?>);
    doughnut('statusChart', <?= json_encode($st['labels']) ?>, <?= json_encode($st['data']) ?>, <?= json_encode(mkColors(count($st['labels']), $colors)) ?>);
    bar('schoolChart', <?= json_encode($sc['labels']) ?>, <?= json_encode($sc['data']) ?>, <?= json_encode(mkColors(count($sc['labels']), $colors)) ?>);
    bar('courseChart', <?= json_encode($cc['labels']) ?>, <?= json_encode($cc['data']) ?>, <?= json_encode(mkColors(count($cc['labels']), $colors)) ?>);
})();
</script>
<?php $extraJs = ob_get_clean(); ?>
