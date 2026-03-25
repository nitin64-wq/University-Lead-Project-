<?php
class NotificationModel {
    public static function forMember(int $memberId, int $limit = 10): array {
        $s = db()->prepare("SELECT * FROM notifications WHERE member_id=? ORDER BY created_at DESC LIMIT ?");
        $s->execute([$memberId, $limit]);
        return $s->fetchAll();
    }
    public static function countUnread(int $memberId): int {
        $s = db()->prepare("SELECT COUNT(*) FROM notifications WHERE member_id=? AND is_read=0");
        $s->execute([$memberId]);
        return (int)$s->fetchColumn();
    }
    public static function markAllRead(int $memberId): void {
        db()->prepare("UPDATE notifications SET is_read=1 WHERE member_id=?")->execute([$memberId]);
    }
    public static function create(int $memberId, string $title, string $message = ''): void {
        db()->prepare("INSERT INTO notifications (member_id,title,message) VALUES (?,?,?)")->execute([$memberId, $title, $message]);
    }
}
