<?php
class CallLogModel {

    public static function create(array $d): int {
        $sql = "INSERT INTO call_logs (lead_id,telecaller_id,call_duration,call_type,lead_status,temperature,warm_level,next_follow_up,remarks,admission_status)
                VALUES (?,?,?,?,?,?,?,?,?,?)";
        $s = db()->prepare($sql);
        $s->execute([
            $d['lead_id'],
            $d['telecaller_id'],
            $d['call_duration']    ?? '',
            $d['call_type']        ?? 'Fresh',
            $d['lead_status']      ?? 'New',
            $d['temperature']      ?? 'Cold',
            $d['warm_level']       ?? '',
            self::parseDate($d['next_follow_up'] ?? ''),
            $d['remarks']          ?? '',
            $d['admission_status'] ?? 'Pending',
        ]);
        return (int)db()->lastInsertId();
    }

    public static function forLead(int $leadId): array {
        $s = db()->prepare("SELECT cl.*, m.name as telecaller_name FROM call_logs cl LEFT JOIN members m ON m.id=cl.telecaller_id WHERE cl.lead_id=? ORDER BY cl.created_at DESC");
        $s->execute([$leadId]);
        return $s->fetchAll();
    }

    public static function forMember(int $memberId, int $limit = 50): array {
        $s = db()->prepare("SELECT cl.*, l.student_name, l.school_name, l.district FROM call_logs cl LEFT JOIN leads l ON l.id=cl.lead_id WHERE cl.telecaller_id=? ORDER BY cl.created_at DESC LIMIT ?");
        $s->execute([$memberId, $limit]);
        return $s->fetchAll();
    }

    public static function statsForMember(int $memberId): array {
        $s = db()->prepare("SELECT
            COUNT(*) as total_calls,
            SUM(lead_status='Interested' OR lead_status='Converted') as interested,
            SUM(lead_status='Converted' OR admission_status='Done') as converted,
            SUM(DATE(created_at)=CURDATE()) as calls_today,
            SUM(WEEK(created_at)=WEEK(NOW())) as calls_this_week
            FROM call_logs WHERE telecaller_id=?");
        $s->execute([$memberId]);
        return $s->fetch();
    }

    public static function weeklyActivity(int $memberId): array {
        $s = db()->prepare("SELECT DATE(created_at) as call_date, COUNT(*) as cnt FROM call_logs WHERE telecaller_id=? AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY call_date ASC");
        $s->execute([$memberId]);
        return $s->fetchAll();
    }

    private static function parseDate(string $val): ?string {
        if (!$val) return null;
        $t = strtotime($val);
        return $t ? date('Y-m-d', $t) : null;
    }
}
