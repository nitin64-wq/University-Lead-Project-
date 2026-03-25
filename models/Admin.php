<?php
class AdminModel {
    public static function findByEmail(string $email): ?array {
        $stmt = db()->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }
    public static function all(): array {
        return db()->query("SELECT id, name, email, created_at FROM admins ORDER BY name")->fetchAll();
    }
}
