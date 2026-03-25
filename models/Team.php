<?php
class TeamModel {
    public static function all(): array {
        return db()->query("SELECT t.*, COUNT(m.id) as member_count FROM teams t LEFT JOIN members m ON m.team_id=t.id GROUP BY t.id ORDER BY t.name")->fetchAll();
    }
    public static function find(int $id): ?array {
        $s = db()->prepare("SELECT * FROM teams WHERE id=?");
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }
    public static function create(array $d): int {
        $s = db()->prepare("INSERT INTO teams (name,description) VALUES (?,?)");
        $s->execute([$d['name'], $d['description'] ?? '']);
        return (int)db()->lastInsertId();
    }
    public static function update(int $id, array $d): void {
        $s = db()->prepare("UPDATE teams SET name=?,description=? WHERE id=?");
        $s->execute([$d['name'], $d['description'] ?? '', $id]);
    }
    public static function delete(int $id): void {
        db()->prepare("DELETE FROM teams WHERE id=?")->execute([$id]);
    }
    public static function leadCount(int $id): int {
        $s = db()->prepare("SELECT COUNT(*) FROM leads WHERE assigned_team_id=?");
        $s->execute([$id]);
        return (int)$s->fetchColumn();
    }
}
