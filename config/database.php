<?php
// ─── Database Configuration ───────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'leadflow_crm');
define('DB_USER', 'root');       // Change to your MySQL username
define('DB_PASS', '');            // Change to your MySQL password
define('DB_CHARSET', 'utf8mb4');

// ─── App Configuration ────────────────────────────────
define('BASE_URL', '/frontend-tech-stack-constraints');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// ─── PDO Connection Singleton ─────────────────────────
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}
