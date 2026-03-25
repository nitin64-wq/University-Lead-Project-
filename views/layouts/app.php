<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'LeadFlow CRM') ?> - LeadFlow CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/style.css" rel="stylesheet">
</head>
<body>
<div id="mainApp" style="display:flex;flex-direction:row;min-height:100vh;width:100%;">

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-bolt"></i>
            <span>LeadFlow</span>
            <button class="sidebar-toggle d-lg-none" onclick="toggleSidebar()"><i class="fas fa-times"></i></button>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><i class="fas fa-user-shield"></i></div>
            <div class="sidebar-user-info">
                <h6><?= e($_SESSION['admin_name'] ?? $_SESSION['member_name'] ?? 'User') ?></h6>
                <small><?= isset($_SESSION['admin_name']) ? 'Administrator' : e($_SESSION['member_role'] ?? 'Telecaller') ?></small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="sidebar-nav-title">MAIN</div>
            <a href="<?= BASE_URL ?>/dashboard"  class="sidebar-link <?= str_contains($title??'','Dashboard')  ?'active':'' ?>"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
            
            <?php if (isset($_SESSION['admin_id'])): ?>
            <a href="<?= BASE_URL ?>/leads/import" class="sidebar-link <?= str_contains($title??'','Import')    ?'active':'' ?>"><i class="fas fa-file-excel"></i><span>Lead Import</span></a>
            <?php endif; ?>
            
            <a href="<?= BASE_URL ?>/leads"        class="sidebar-link <?= ($title??'')==='Leads List'          ?'active':'' ?>"><i class="fas fa-list-alt"></i><span>Leads List</span></a>
            
            <?php if (isset($_SESSION['admin_id'])): ?>
            <div class="sidebar-nav-title">USERS</div>
            <a href="<?= BASE_URL ?>/members"      class="sidebar-link <?= str_contains($title??'','Member')    ?'active':'' ?>"><i class="fas fa-headset"></i><span>Telecallers</span></a>
            <div class="sidebar-nav-title">OPERATIONS</div>
            <a href="<?= BASE_URL ?>/assignment"   class="sidebar-link <?= str_contains($title??'','Assignment')?'active':'' ?>"><i class="fas fa-tasks"></i><span>Lead Assignment</span></a>
            <a href="<?= BASE_URL ?>/analytics"    class="sidebar-link <?= str_contains($title??'','Analytics') ?'active':'' ?>"><i class="fas fa-chart-bar"></i><span>Analytics</span></a>
            <div class="sidebar-nav-title">REPORTS</div>
            <a href="<?= BASE_URL ?>/reports/hot-warm" class="sidebar-link <?= str_contains($title??'','HOT') ?'active':'' ?>"><i class="fas fa-fire"></i><span>HOT &amp; WARM</span></a>
            <a href="<?= BASE_URL ?>/reports/daily"    class="sidebar-link <?= str_contains($title??'','Daily') ?'active':'' ?>"><i class="fas fa-phone-volume"></i><span>Daily Calling</span></a>
            <?php endif; ?>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= BASE_URL ?>/logout" class="sidebar-link text-danger"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link sidebar-toggle-btn d-lg-none me-2" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div class="search-box d-none d-md-block">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search leads..." id="globalSearch">
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="user-avatar-sm"><i class="fas fa-user"></i></div>
                <span class="d-none d-md-inline fw-semibold"><?= e($_SESSION['admin_name'] ?? $_SESSION['member_name'] ?? 'User') ?></span>
            </div>
        </nav>

        <!-- Flash message -->
        <?php $flash = getFlash(); if ($flash): ?>
        <div class="mx-4 mt-3">
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                <?= e($flash['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php endif; ?>

        <div class="content-wrapper">
            <?= $content ?>
        </div>
    </main>
</div>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
    document.getElementById('sidebarOverlay').classList.toggle('active');
}
// Confirm delete
document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const href = this.href;
        Swal.fire({title:'Are you sure?',text:'This cannot be undone.',icon:'warning',showCancelButton:true,confirmButtonColor:'#ef4444',confirmButtonText:'Delete'})
            .then(r => { if(r.isConfirmed) window.location.href = href; });
    });
});
</script>
<?php if (isset($extraJs)) echo $extraJs; ?>
</body>
</html>
