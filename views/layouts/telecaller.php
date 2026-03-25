<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Telecaller') ?> - LeadFlow CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/style.css" rel="stylesheet">
</head>
<body>
<div id="mainApp" style="display:flex;flex-direction:row;min-height:100vh;width:100%;">

    <!-- Sidebar — identical structure to admin -->
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-bolt"></i>
            <span>LeadFlow</span>
            <button class="sidebar-toggle d-lg-none" onclick="toggleSidebar()"><i class="fas fa-times"></i></button>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><i class="fas fa-headset"></i></div>
            <div class="sidebar-user-info">
                <h6><?= e($_SESSION['member_name'] ?? 'Telecaller') ?></h6>
                <small><?= e($_SESSION['member_role'] ?? 'Telecaller') ?></small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="sidebar-nav-title">MAIN</div>
            <a href="<?= BASE_URL ?>/tc/dashboard" class="sidebar-link <?= ($title??'')==='My Dashboard' ?'active':'' ?>">
                <i class="fas fa-th-large"></i><span>Dashboard</span>
            </a>
            <a href="<?= BASE_URL ?>/tc/leads" class="sidebar-link <?= ($title??'')==='My Leads' ?'active':'' ?>">
                <i class="fas fa-users"></i><span>My Leads</span>
            </a>
            <div class="sidebar-nav-title">FOLLOW-UPS</div>
            <a href="<?= BASE_URL ?>/tc/followups?tab=today" class="sidebar-link <?= ($title??'')==='Follow-ups' && ($_GET['tab']??'today')==='today' ?'active':'' ?>">
                <i class="fas fa-bell"></i><span>Today</span>
                <?php $todayFollowups = $followups['today'] ?? ($counts['today'] ?? 0); if ($todayFollowups > 0): ?>
                <span class="ms-auto badge rounded-pill" style="background:#ef4444;font-size:10px;"><?= (int)$todayFollowups ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= BASE_URL ?>/tc/followups?tab=upcoming" class="sidebar-link <?= ($title??'')==='Follow-ups' && ($_GET['tab']??'')==='upcoming' ?'active':'' ?>">
                <i class="fas fa-clock"></i><span>Upcoming</span>
            </a>
            <a href="<?= BASE_URL ?>/tc/followups?tab=overdue" class="sidebar-link <?= ($title??'')==='Follow-ups' && ($_GET['tab']??'')==='overdue' ?'active':'' ?>">
                <i class="fas fa-exclamation-triangle"></i><span>Overdue</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= BASE_URL ?>/logout" class="sidebar-link text-danger"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar — identical structure to admin -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link sidebar-toggle-btn d-lg-none me-2" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div class="d-none d-md-flex align-items-center gap-2" style="color:var(--text-muted);font-size:13px;font-weight:500;">
                    <i class="fas fa-headset" style="color:var(--primary);font-size:15px;"></i>
                    Telecaller Portal
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Notification Bell -->
                <?php
                $bellCount     = $unreadCount ?? 0;
                $notifications = $notifications ?? [];
                ?>
                <div class="dropdown">
                    <a href="#" class="notification-btn" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <?php if ($bellCount > 0): ?>
                        <span class="notification-badge"><?= $bellCount ?></span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu notification-dropdown dropdown-menu-end" aria-labelledby="notifDropdown">
                        <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                            <strong style="font-size:14px;">Notifications</strong>
                            <?php if ($bellCount > 0): ?>
                            <a href="<?= BASE_URL ?>/tc/notifications-read" class="text-primary" style="font-size:12px;">Mark all read</a>
                            <?php endif; ?>
                        </li>
                        <?php if (empty($notifications)): ?>
                        <li class="px-3 py-3 text-center text-muted" style="font-size:13px;">No new notifications</li>
                        <?php else: ?>
                        <?php foreach ($notifications as $n): ?>
                        <li class="dropdown-item <?= $n['is_read'] ? '' : 'fw-semibold' ?>">
                            <div style="font-size:13px;"><?= e($n['title']) ?></div>
                            <?php if ($n['message']): ?><div class="text-muted" style="font-size:11px;"><?= e($n['message']) ?></div><?php endif; ?>
                            <div class="text-muted" style="font-size:11px;"><?= date('d M, h:i A', strtotime($n['created_at'])) ?></div>
                        </li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="user-avatar-sm"><i class="fas fa-user"></i></div>
                <span class="d-none d-md-inline fw-semibold"><?= e($_SESSION['member_name'] ?? 'Telecaller') ?></span>
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
</script>
<?php if (isset($extraJs)) echo $extraJs; ?>
</body>
</html>
