<?php $summary = $_SESSION['import_summary'] ?? null; unset($_SESSION['import_summary']); ?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-file-excel me-2"></i>Lead Import</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Home</a></li><li class="breadcrumb-item active">Import</li></ol></nav>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card dashboard-card">
            <div class="card-header"><h6><i class="fas fa-upload me-2"></i>Upload Excel File</h6></div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/leads/import" enctype="multipart/form-data" id="importForm">
                    <div class="upload-zone" id="uploadZone" onclick="document.getElementById('excelFile').click()">
                        <div class="upload-icon"><i class="fas fa-cloud-arrow-up"></i></div>
                        <h5>Drag & Drop your Excel file here</h5>
                        <p class="text-muted">or click to browse files</p>
                        <input type="file" name="excel_file" id="excelFile" accept=".xlsx,.xls,.csv" class="d-none" onchange="showFile(this)">
                        <p class="mt-2 small text-muted">Supports .xlsx, .xls, .csv files</p>
                    </div>
                    <div id="fileInfo" class="file-info mt-3 d-none">
                        <div class="d-flex align-items-center gap-3">
                            <div class="file-icon"><i class="fas fa-file-excel"></i></div>
                            <div><h6 id="fileName" class="mb-0">-</h6><small id="fileSize" class="text-muted">-</small></div>
                            <button type="button" class="btn btn-sm btn-outline-danger ms-auto" onclick="clearFile()"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label text-muted">Excel File Created By Person</label>
                        <input type="text" name="excel_created_by" class="form-control" placeholder="Enter person name..." required>
                    </div>
                    <button type="submit" id="importBtn" class="btn btn-success btn-lg w-100 mt-3 d-none">
                        <i class="fas fa-file-import me-2"></i>Import Leads
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <?php if ($summary): ?>
        <div class="card dashboard-card">
            <div class="card-header bg-success text-white"><h6 class="text-white mb-0"><i class="fas fa-check-circle me-2"></i>Import Summary</h6></div>
            <div class="card-body">
                <div class="summary-item"><span>Total Records</span><strong><?= $summary['total'] ?></strong></div>
                <div class="summary-item"><span>Imported</span><strong class="text-success"><?= $summary['imported'] ?></strong></div>
                <div class="summary-item"><span>Duplicates Skipped</span><strong class="text-warning"><?= $summary['duplicates'] ?></strong></div>
                <div class="summary-item"><span>Errors</span><strong class="text-danger"><?= $summary['errors'] ?></strong></div>
            </div>
        </div>
        <?php endif; ?>
        <div class="card dashboard-card <?= $summary ? 'mt-4' : '' ?>">
            <div class="card-header"><h6><i class="fas fa-info-circle me-2"></i>Required Columns</h6></div>
            <div class="card-body">
                <ul class="guide-list">
                    <?php foreach (['Sr. No','Student Name','Father Name','Contact Number (Student)','Contact Number (Parents)','Stream','Reserve for Future','Category','School Name','Name of Team'] as $col): ?>
                    <li><i class="fas fa-check text-success"></i> <?= $col ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
function showFile(el) {
    if (!el.files[0]) return;
    document.getElementById('fileName').textContent = el.files[0].name;
    document.getElementById('fileSize').textContent = (el.files[0].size/1024).toFixed(1) + ' KB';
    document.getElementById('fileInfo').classList.remove('d-none');
    document.getElementById('importBtn').classList.remove('d-none');
}
function clearFile() {
    document.getElementById('excelFile').value = '';
    document.getElementById('fileInfo').classList.add('d-none');
    document.getElementById('importBtn').classList.add('d-none');
}
const zone = document.getElementById('uploadZone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
zone.addEventListener('drop', e => { e.preventDefault(); zone.classList.remove('dragover'); document.getElementById('excelFile').files = e.dataTransfer.files; showFile(document.getElementById('excelFile')); });
</script>
