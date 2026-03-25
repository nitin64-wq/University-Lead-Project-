<?php
// ─── Bootstrap ────────────────────────────────────────
session_start();
require_once __DIR__ . '/config/database.php';

// Auto-load models & controllers
foreach (glob(__DIR__ . '/models/*.php')      as $f) require_once $f;
foreach (glob(__DIR__ . '/controllers/*.php') as $f) require_once $f;

// Composer autoloader (PhpSpreadsheet)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// ─── Helpers ─────────────────────────────────────────
function requireAuth(): void {
    if (empty($_SESSION['admin_id']) && empty($_SESSION['member_id'])) redirect('/login');
}

function requireAdmin(): void {
    if (empty($_SESSION['admin_id'])) { flash('Admin access required.', 'danger'); redirect('/login'); }
}

function redirect(string $path): void {
    header('Location: ' . BASE_URL . $path);
    exit;
}

function flash(string $msg, string $type = 'info'): void {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function getFlash(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function render(string $view, array $data = []): void {
    extract($data);
    ob_start();
    require __DIR__ . '/views/' . $view . '.php';
    $content = ob_get_clean();
    // No wrap for login
    if (str_starts_with($view, 'auth/')) { echo $content; return; }
    // Telecaller views use their own layout
    if (str_starts_with($view, 'telecaller/')) {
        require __DIR__ . '/views/layouts/telecaller.php';
        return;
    }
    require __DIR__ . '/views/layouts/app.php';
}

function e(mixed $v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES); }

// ─── Router ───────────────────────────────────────────
$route  = trim($_GET['route'] ?? '', '/');
$method = $_SERVER['REQUEST_METHOD'];

// Normalize: strip query string from route
$route = explode('?', $route)[0];

$segments = explode('/', $route);
$r0 = $segments[0] ?? '';
$r1 = $segments[1] ?? '';
$r2 = $segments[2] ?? '';
$id  = isset($segments[2]) ? (int)$segments[2] : ((is_numeric($r1) ? (int)$r1 : 0));

match(true) {
    // ── Auth ─────────────────────────────────────────
    ($route === '' || $r0 === 'login')  => (new AuthController())->login(),
    ($r0 === 'logout')                   => (new AuthController())->logout(),

    // ── Admin Dashboard ──────────────────────────────
    ($r0 === 'dashboard')                => (new DashboardController())->index(),

    // ── Admin Reports ────────────────────────────────
    ($r0 === 'reports' && $r1 === 'hot-warm') => (new ReportController())->hotWarm(),
    ($r0 === 'reports' && $r1 === 'daily')    => (new ReportController())->daily(),

    // ── Telecaller Routes (tc/) ──────────────────────
    ($r0 === 'tc' && $r1 === 'dashboard')                                     => (new TelecallerController())->dashboard(),
    ($r0 === 'tc' && $r1 === 'leads' && is_numeric($r2) && isset($segments[3]) && $segments[3]==='call' && $method === 'POST')         => (new TelecallerController())->logCall((int)$r2),
    ($r0 === 'tc' && $r1 === 'leads' && is_numeric($r2) && isset($segments[3]) && $segments[3]==='quick-update' && $method === 'POST') => (new TelecallerController())->quickUpdate((int)$r2),
    ($r0 === 'tc' && $r1 === 'leads' && is_numeric($r2) && isset($segments[3]) && $segments[3]==='edit' && $method === 'POST')         => (new TelecallerController())->saveLead((int)$r2),
    ($r0 === 'tc' && $r1 === 'leads' && is_numeric($r2) && isset($segments[3]) && $segments[3]==='edit')                              => (new TelecallerController())->editLead((int)$r2),
    ($r0 === 'tc' && $r1 === 'leads' && is_numeric($r2))                      => (new TelecallerController())->leadDetail((int)$r2),
    ($r0 === 'tc' && $r1 === 'leads')                                         => (new TelecallerController())->leads(),
    ($r0 === 'tc' && $r1 === 'followups')                                     => (new TelecallerController())->followups(),
    ($r0 === 'tc' && $r1 === 'notifications-read')                            => (new TelecallerController())->markNotificationsRead(),

    // ── Leads ────────────────────────────────────────
    ($r0 === 'leads' && $r1 === 'import' && $method === 'GET')  => (new LeadController())->import(),
    ($r0 === 'leads' && $r1 === 'import' && $method === 'POST') => (new LeadController())->import(),
    ($r0 === 'leads' && $r1 === 'create' && $method === 'GET')  => (new LeadController())->create(),
    ($r0 === 'leads' && $r1 === 'create' && $method === 'POST') => (new LeadController())->create(),
    ($r0 === 'leads' && is_numeric($r1) && isset($segments[2]) && $segments[2] === 'edit') => (new LeadController())->edit((int)$r1),
    ($r0 === 'leads' && is_numeric($r1) && isset($segments[2]) && $segments[2] === 'delete') => (new LeadController())->delete((int)$r1),
    ($r0 === 'leads' && is_numeric($r1) && empty($segments[2])) => (new LeadController())->show((int)$r1),
    ($r0 === 'leads')                    => (new LeadController())->index(),

    // ── Teams ────────────────────────────────────────
    ($r0 === 'teams' && $r1 === 'store')                              => (new TeamController())->store(),
    ($r0 === 'teams' && is_numeric($r1) && $segments[2]==='update')   => (new TeamController())->update((int)$r1),
    ($r0 === 'teams' && is_numeric($r1) && $segments[2]==='delete')   => (new TeamController())->delete((int)$r1),
    ($r0 === 'teams')                    => (new TeamController())->index(),

    // ── Members ──────────────────────────────────────
    ($r0 === 'members' && $r1 === 'store')                              => (new MemberController())->store(),
    ($r0 === 'members' && is_numeric($r1) && $segments[2]==='update')   => (new MemberController())->update((int)$r1),
    ($r0 === 'members' && is_numeric($r1) && $segments[2]==='move')     => (new MemberController())->move((int)$r1),
    ($r0 === 'members' && is_numeric($r1) && $segments[2]==='delete')   => (new MemberController())->delete((int)$r1),
    ($r0 === 'members')                  => (new MemberController())->index(),

    // ── Assignment ───────────────────────────────────
    ($r0 === 'assignment' && $r1 === 'assign')            => (new AssignmentController())->assign(),
    ($r0 === 'assignment' && $r1 === 'assign-by-school')  => (new AssignmentController())->assignBySchool(),
    ($r0 === 'assignment')               => (new AssignmentController())->index(),

    // ── Analytics ────────────────────────────────────
    ($r0 === 'analytics')                => (new AnalyticsController())->index(),

    // ── 404 ──────────────────────────────────────────
    default => (function() { http_response_code(404); echo '<h1>404 - Page Not Found</h1>'; })()
};
