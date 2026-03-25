<?php
class MemberModel {
    public static function all(?int $teamId = null, ?string $shift = null, ?string $search = null): array {
        $where = []; $params = [];
        if ($teamId)  { $where[] = "m.team_id=?";  $params[] = $teamId; }
        if ($shift)   { $where[] = "m.shift=?";     $params[] = $shift; }
        if ($search)  { $where[] = "(m.name LIKE ? OR m.email LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        $sql = "SELECT m.*, t.name as team_name FROM members m LEFT JOIN teams t ON t.id=m.team_id";
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY m.name";
        $s = db()->prepare($sql);
        $s->execute($params);
        return $s->fetchAll();
    }
    public static function find(int $id): ?array {
        $s = db()->prepare("SELECT m.*, t.name as team_name FROM members m LEFT JOIN teams t ON t.id=m.team_id WHERE m.id=?");
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }
    public static function findByEmail(string $email): ?array {
        $s = db()->prepare("SELECT * FROM members WHERE email=? LIMIT 1");
        $s->execute([$email]);
        return $s->fetch() ?: null;
    }
    public static function create(array $d): int {
        $hash = password_hash($d['password'], PASSWORD_BCRYPT);
        $s = db()->prepare("INSERT INTO members (name,email,phone,password,role,team_id,shift) VALUES (?,?,?,?,?,?,?)");
        $s->execute([$d['name'],$d['email'],$d['phone'],$hash,$d['role'],$d['team_id'],$d['shift']]);
        return (int)db()->lastInsertId();
    }
    public static function update(int $id, array $d): void {
        $params = [$d['name'],$d['email'],$d['phone'],$d['role'],$d['team_id'],$d['shift']];
        $sql = "UPDATE members SET name=?,email=?,phone=?,role=?,team_id=?,shift=?";
        if (!empty($d['password'])) {
            $sql .= ",password=?";
            $params[] = password_hash($d['password'], PASSWORD_BCRYPT);
        }
        $sql .= " WHERE id=?";
        $params[] = $id;
        db()->prepare($sql)->execute($params);
    }
    public static function moveTo(int $id, int $teamId): void {
        db()->prepare("UPDATE members SET team_id=? WHERE id=?")->execute([$teamId,$id]);
    }
    public static function delete(int $id): void {
        db()->prepare("DELETE FROM members WHERE id=?")->execute([$id]);
    }
}
