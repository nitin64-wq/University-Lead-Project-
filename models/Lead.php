<?php
class LeadModel {

    public static function paginate(array $filters, int $page = 1, int $perPage = 15): array {
        [$where, $params] = self::buildWhere($filters);
        $offset = ($page - 1) * $perPage;
        $countSql = "SELECT COUNT(*) FROM leads l" . ($where ? " WHERE $where" : "");
        $total = (int)db()->prepare($countSql)->execute($params) ? db()->prepare($countSql)->execute($params) : 0;
        $cs = db()->prepare($countSql); $cs->execute($params);
        $total = (int)$cs->fetchColumn();

        $sql = "SELECT l.*, t.name as team_name, m.name as member_name FROM leads l LEFT JOIN teams t ON t.id=l.assigned_team_id LEFT JOIN members m ON m.id=l.assigned_member_id";
        if ($where) $sql .= " WHERE $where";
        $sql .= " ORDER BY l.id DESC LIMIT $perPage OFFSET $offset";
        $s = db()->prepare($sql); $s->execute($params);
        return ['data' => $s->fetchAll(), 'total' => $total, 'pages' => ceil($total / $perPage), 'page' => $page];
    }

    public static function all(array $filters = []): array {
        [$where, $params] = self::buildWhere($filters);
        $sql = "SELECT l.*, t.name as team_name, m.name as member_name FROM leads l LEFT JOIN teams t ON t.id=l.assigned_team_id LEFT JOIN members m ON m.id=l.assigned_member_id";
        if ($where) $sql .= " WHERE $where";
        $sql .= " ORDER BY l.id DESC";
        $s = db()->prepare($sql); $s->execute($params);
        return $s->fetchAll();
    }

    /**
     * Returns the next lead ID (after $currentId) assigned to this member/team,
     * ordered ascending by id so the telecaller works through the list top-to-bottom.
     */
    public static function nextId(int $currentId, int $memberId, ?int $teamId): ?int {
        if ($teamId) {
            $sql = "SELECT id FROM leads
                    WHERE id > ? AND (assigned_member_id = ? OR assigned_team_id = ?)
                    ORDER BY id ASC LIMIT 1";
            $s = db()->prepare($sql);
            $s->execute([$currentId, $memberId, $teamId]);
        } else {
            $sql = "SELECT id FROM leads
                    WHERE id > ? AND assigned_member_id = ?
                    ORDER BY id ASC LIMIT 1";
            $s = db()->prepare($sql);
            $s->execute([$currentId, $memberId]);
        }
        $row = $s->fetch();
        return $row ? (int)$row['id'] : null;
    }

    private static function buildWhere(array $f): array {
        $where = []; $params = [];
        if (!empty($f['school']))     { $where[] = "l.school_name=?";       $params[] = $f['school']; }
        if (!empty($f['district']))   { $where[] = "l.district=?";          $params[] = $f['district']; }
        if (!empty($f['course']))     { $where[] = "l.course_interested=?"; $params[] = $f['course']; }
        if (!empty($f['temp']))       { $where[] = "l.temperature=?";       $params[] = $f['temp']; }
        if (!empty($f['status']))     { $where[] = "l.lead_status=?";       $params[] = $f['status']; }
        if (!empty($f['team_id']))    { $where[] = "l.assigned_team_id=?";  $params[] = $f['team_id']; }
        if (!empty($f['member_id']))  { $where[] = "l.assigned_member_id=?";$params[] = $f['member_id']; }

        // Telecaller view: team-assigned OR individually-assigned
        if (!empty($f['tc_team_id']) && !empty($f['tc_member_id'])) {
            $where[] = "(l.assigned_team_id = ? OR l.assigned_member_id = ?)";
            $params[] = (int)$f['tc_team_id'];
            $params[] = (int)$f['tc_member_id'];
        } elseif (!empty($f['tc_team_id'])) {
            $where[] = "l.assigned_team_id = ?";
            $params[] = (int)$f['tc_team_id'];
        } elseif (!empty($f['tc_member_id'])) {
            $where[] = "l.assigned_member_id = ?";
            $params[] = (int)$f['tc_member_id'];
        }

        if (!empty($f['followup_today']))    { $where[] = "l.next_follow_up = CURDATE()"; }
        if (!empty($f['followup_upcoming'])) { $where[] = "l.next_follow_up > CURDATE()"; }
        if (!empty($f['followup_overdue']))  { $where[] = "l.next_follow_up < CURDATE() AND l.next_follow_up IS NOT NULL"; }
        if (!empty($f['search']))     {
            $where[] = "(l.student_name LIKE ? OR l.student_contact LIKE ? OR l.school_name LIKE ? OR l.district LIKE ?)";
            $s = "%{$f['search']}%"; $params = array_merge($params, [$s,$s,$s,$s]);
        }
        return [implode(' AND ', $where), $params];
    }

    /**
     * Stats for a telecaller — shows leads from their team OR individually assigned.
     * @param int $memberId  The member's own ID
     * @param int|null $teamId The team the member belongs to
     */
    public static function statsForMember(int $memberId, ?int $teamId = null): array {
        [$cond, $params] = self::memberLeadCondition($memberId, $teamId);
        $s = db()->prepare("SELECT
            COUNT(*) as total,
            SUM(temperature='Hot') as hot,
            SUM(temperature='Warm') as warm,
            SUM(temperature='Cold') as cold,
            SUM(next_follow_up = CURDATE()) as today_followup,
            SUM(admission_status='Done') as admitted
            FROM leads WHERE $cond");
        $s->execute($params);
        return $s->fetch() ?: [];
    }

    public static function assignMember(array $leadIds, int $memberId, int $adminId): int {
        $count = 0;
        $upd = db()->prepare("UPDATE leads SET assigned_member_id=? WHERE id=?");
        foreach ($leadIds as $lid) {
            $lid = (int)$lid;
            $upd->execute([$memberId, $lid]);
            $count++;
        }
        return $count;
    }

    public static function countFollowups(int $memberId, ?int $teamId = null): array {
        [$cond, $params] = self::memberLeadCondition($memberId, $teamId);
        $s = db()->prepare("SELECT
            SUM(next_follow_up = CURDATE()) as today,
            SUM(next_follow_up > CURDATE()) as upcoming,
            SUM(next_follow_up < CURDATE() AND next_follow_up IS NOT NULL) as overdue
            FROM leads WHERE $cond");
        $s->execute($params);
        return $s->fetch() ?: ['today'=>0,'upcoming'=>0,'overdue'=>0];
    }

    /**
     * Builds a WHERE condition that returns leads visible to a telecaller:
     * leads assigned to their team OR individually assigned to them.
     */
    private static function memberLeadCondition(int $memberId, ?int $teamId): array {
        if ($teamId) {
            return [
                '(assigned_team_id = ? OR assigned_member_id = ?)',
                [$teamId, $memberId]
            ];
        }
        return ['assigned_member_id = ?', [$memberId]];
    }

    public static function find(int $id): ?array {
        $s = db()->prepare("SELECT l.*, t.name as team_name FROM leads l LEFT JOIN teams t ON t.id=l.assigned_team_id WHERE l.id=?");
        $s->execute([$id]); return $s->fetch() ?: null;
    }

    public static function create(array $d): int {
        $sql = "INSERT INTO leads (student_name,father_name,student_contact,parent_contact,stream,category,school_name,district,village,course_interested,telecaller_name,call_duration,call_type,availability_date,lead_status,temperature,warm_level,next_follow_up,remarks,admission_status,excel_created_by)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $s = db()->prepare($sql);
        $s->execute([
            $d['student_name'], $d['father_name'] ?? '', $d['student_contact'] ?? '', $d['parent_contact'] ?? '',
            $d['stream'] ?? '', $d['category'] ?? '', $d['school_name'] ?? '', $d['district'] ?? '',
            $d['village'] ?? '', $d['course_interested'] ?? '', $d['telecaller_name'] ?? '',
            $d['call_duration'] ?? '', $d['call_type'] ?? 'Fresh',
            self::parseDate($d['availability_date'] ?? ''),
            $d['lead_status'] ?? 'New', $d['temperature'] ?? 'Cold', $d['warm_level'] ?? '',
            self::parseDate($d['next_follow_up'] ?? ''),
            $d['remarks'] ?? '', $d['admission_status'] ?? 'Pending', $d['excel_created_by'] ?? null
        ]);
        return (int)db()->lastInsertId();
    }

    public static function update(int $id, array $d): void {
        $sql = "UPDATE leads SET student_name=?,father_name=?,student_contact=?,parent_contact=?,stream=?,category=?,school_name=?,district=?,village=?,course_interested=?,telecaller_name=?,call_duration=?,call_type=?,availability_date=?,lead_status=?,temperature=?,warm_level=?,next_follow_up=?,remarks=?,admission_status=?,excel_created_by=? WHERE id=?";
        db()->prepare($sql)->execute([
            $d['student_name'], $d['father_name'] ?? '', $d['student_contact'] ?? '', $d['parent_contact'] ?? '',
            $d['stream'] ?? '', $d['category'] ?? '', $d['school_name'] ?? '', $d['district'] ?? '',
            $d['village'] ?? '', $d['course_interested'] ?? '', $d['telecaller_name'] ?? '',
            $d['call_duration'] ?? '', $d['call_type'] ?? 'Fresh',
            self::parseDate($d['availability_date'] ?? ''),
            $d['lead_status'] ?? 'New', $d['temperature'] ?? 'Cold', $d['warm_level'] ?? '',
            self::parseDate($d['next_follow_up'] ?? ''),
            $d['remarks'] ?? '', $d['admission_status'] ?? 'Pending', $d['excel_created_by'] ?? null, $id
        ]);
    }

    /** Telecaller-only patch: update call-related columns only */
    public static function updateCallInfo(int $id, array $d): void {
        $sql = "UPDATE leads SET
            call_duration=?, call_type=?, lead_status=?,
            temperature=?, warm_level=?, next_follow_up=?,
            remarks=?, admission_status=?, last_call_date=?
            WHERE id=?";
        db()->prepare($sql)->execute([
            $d['call_duration']   ?? '',
            $d['call_type']       ?? 'Fresh',
            $d['lead_status']     ?? 'Contacted',
            $d['temperature']     ?? 'Cold',
            $d['warm_level']      ?? '',
            self::parseDate($d['next_follow_up'] ?? ''),
            $d['remarks']         ?? '',
            $d['admission_status']?? 'Pending',
            $d['last_call_date']  ?? date('Y-m-d'),
            $id
        ]);
    }

    public static function delete(int $id): void {
        db()->prepare("DELETE FROM leads WHERE id=?")->execute([$id]);
    }

    public static function assignTeam(array $leadIds, int $teamId, int $adminId): int {
        $count = 0;
        $upd = db()->prepare("UPDATE leads SET assigned_team_id=? WHERE id=?");
        $log = db()->prepare("INSERT INTO lead_assignments (lead_id,team_id,assigned_by) VALUES (?,?,?) ON DUPLICATE KEY UPDATE team_id=?, assigned_by=?");
        foreach ($leadIds as $lid) {
            $lid = (int)$lid;
            $upd->execute([$teamId, $lid]);
            $log->execute([$lid, $teamId, $adminId, $teamId, $adminId]);
            $count++;
        }
        return $count;
    }

    public static function assignBySchool(string $school, int $memberId, int $adminId): int {
        $s = db()->prepare("SELECT id FROM leads WHERE school_name=?");
        $s->execute([$school]);
        $ids = array_column($s->fetchAll(), 'id');
        return empty($ids) ? 0 : self::assignMember($ids, $memberId, $adminId);
    }

    /** Update a single safe column on a lead row */
    public static function patchField(int $id, string $field, mixed $value): void {
        db()->prepare("UPDATE leads SET `$field`=? WHERE id=?")->execute([$value, $id]);
    }

    public static function isDuplicate(string $name, string $contact): bool {
        $s = db()->prepare("SELECT id FROM leads WHERE student_name=? AND student_contact=? LIMIT 1");
        $s->execute([$name, $contact]);
        return (bool)$s->fetch();
    }

    // ─── Analytics ────────────────────────────────────
    public static function countBy(string $col): array {
        $s = db()->query("SELECT $col as label, COUNT(*) as cnt FROM leads WHERE $col IS NOT NULL AND $col<>'' GROUP BY $col ORDER BY cnt DESC");
        return $s->fetchAll();
    }
    public static function stats(): array {
        $row = db()->query("SELECT
            COUNT(*) as total,
            SUM(temperature='Hot') as hot,
            SUM(temperature='Warm') as warm,
            SUM(temperature='Cold') as cold,
            SUM(admission_status='Done') as admitted,
            SUM(lead_status='Follow Up') as pending_followup
        FROM leads")->fetch();
        return $row;
    }
    public static function distinctValues(string $col): array {
        $s = db()->query("SELECT DISTINCT $col as v FROM leads WHERE $col<>'' AND $col IS NOT NULL ORDER BY $col");
        return array_column($s->fetchAll(), 'v');
    }

    private static function parseDate(string $val): ?string {
        if (!$val) return null;
        $t = strtotime($val);
        return $t ? date('Y-m-d', $t) : null;
    }
}
