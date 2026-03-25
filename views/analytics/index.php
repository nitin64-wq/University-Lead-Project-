<?php
// Re-use chart helper
function chartJsonFull(array $rows, string $limit = ''): string {
    if ($limit) $rows = array_slice($rows, 0, (int)$limit);
    $labels = array_column($rows, 'label');
    $data   = array_column($rows, 'cnt');
    return json_encode(['labels' => $labels, 'data' => $data]);
}
?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-chart-bar me-2"></i>Analytics</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Home</a></li><li class="breadcrumb-item active">Analytics</li></ol></nav>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="card dashboard-card">
            <div class="card-header"><h6><i class="fas fa-chart-bar me-2"></i>Leads by District</h6></div>
            <div class="card-body"><canvas id="analyticsDistrictChart" height="280"></canvas></div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card dashboard-card">
            <div class="card-header"><h6><i class="fas fa-chart-bar me-2"></i>Leads by School (Top 20)</h6></div>
            <div class="card-body"><canvas id="analyticsSchoolChart" height="280"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-4">
        <div class="card dashboard-card">
            <div class="card-header"><h6><i class="fas fa-chart-pie me-2"></i>Temperature Breakdown</h6></div>
            <div class="card-body"><canvas id="analyticsTempChart" height="260"></canvas></div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card dashboard-card">
            <div class="card-header"><h6><i class="fas fa-table me-2"></i>Detailed School Metrics</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow: auto;">
                    <table class="table table-hover mb-0 text-sm">
                        <thead class="sticky-top bg-white">
                            <tr><th>School Name</th><th>Total Leads</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($bySchool as $s): ?>
                            <tr><td><strong><?= e($s['label']) ?></strong></td><td><?= e($s['cnt']) ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$colors = ['#4f46e5','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#0d9488','#ec4899','#84cc16'];
function genColors(int $n, array $palette): array { $out=[]; for($i=0;$i<$n;$i++) $out[]=$palette[$i%count($palette)]; return $out; }

$dC = json_decode(chartJsonFull($byDistrict), true);
$sC = json_decode(chartJsonFull($bySchool, '20'), true);
$tC = json_decode(chartJsonFull($byTemp), true);

ob_start(); 
?>
<script>
(function() {
    const bar = (id, labels, data, colors) => new Chart(document.getElementById(id), {
        type:'bar', data:{labels, datasets:[{data, backgroundColor:colors, borderRadius:6}]},
        options:{plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}, responsive:true}
    });
    const pie = (id, labels, data, colors) => new Chart(document.getElementById(id), {
        type:'pie', data:{labels, datasets:[{data, backgroundColor:colors}]},
        options:{plugins:{legend:{position:'bottom'}}, responsive:true}
    });
    bar('analyticsDistrictChart', <?= json_encode($dC['labels']) ?>, <?= json_encode($dC['data']) ?>, <?= json_encode(genColors(count($dC['labels']), $colors)) ?>);
    bar('analyticsSchoolChart', <?= json_encode($sC['labels']) ?>, <?= json_encode($sC['data']) ?>, <?= json_encode(genColors(count($sC['labels']), $colors)) ?>);
    pie('analyticsTempChart', <?= json_encode($tC['labels']) ?>, <?= json_encode($tC['data']) ?>, <?= json_encode(genColors(count($tC['labels']), $colors)) ?>);
})();
</script>
<?php $extraJs = ob_get_clean(); ?>
