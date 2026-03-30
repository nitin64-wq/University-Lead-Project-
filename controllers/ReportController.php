<?php
class ReportController {

    private function requireAdmin(): void {
        requireAuth();
        if (empty($_SESSION['admin_id'])) {
            flash('Access denied.', 'danger');
            redirect('/dashboard');
        }
    }

    // ── DateWise HOT & WARM Queries Report ───────────────────
    public function hotWarm(): void {
        $this->requireAdmin();
        $date    = $_GET['date'] ?? date('Y-m-d');
        $members = MemberModel::all();

        // Leads that are HOT or Warm on the selected date (created or called on that date)
        $sql = "SELECT m.name AS caller_name, l.*
                FROM members m
                LEFT JOIN leads l ON m.id = l.assigned_member_id 
                                  AND LOWER(l.temperature) IN ('hot','warm')
                                  AND DATE(l.last_call_date) = ?
                ORDER BY m.name, l.id";
        $s = db()->prepare($sql);
        $s->execute([$date]);
        $leads = $s->fetchAll();

        render('reports/hot_warm', compact('leads','date','members') + ['title' => 'HOT & WARM Report']);
    }

    // ── Daily Calling Summary Report ─────────────────────────
    public function daily(): void {
        $this->requireAdmin();
        $date    = $_GET['date'] ?? date('Y-m-d');

        // Per-telecaller summary for the selected date
        $sql = "SELECT
                    m.name   AS caller_name,
                    ? AS call_date,
                    SUM(CASE WHEN LOWER(l.temperature)='hot'              THEN 1 ELSE 0 END) AS hot_calls,
                    SUM(CASE WHEN LOWER(l.temperature)='warm'             THEN 1 ELSE 0 END) AS warm_calls,
                    SUM(CASE WHEN LOWER(l.temperature)='cold'             THEN 1 ELSE 0 END) AS cold_calls,
                    SUM(CASE WHEN LOWER(l.temperature)='not communicated' THEN 1 ELSE 0 END) AS nc_calls,
                    SUM(CASE WHEN LOWER(l.lead_status)='converted'        THEN 1 ELSE 0 END) AS admissions,
                    COUNT(l.id)                                                        AS total_calls,
                    SUM(CASE WHEN LOWER(l.temperature) IN ('hot','warm','cold') THEN 1 ELSE 0 END) AS effective_calls,
                    SUM(COALESCE(l.call_duration, 0))                                  AS total_minutes
                FROM members m
                LEFT JOIN leads l ON m.id = l.assigned_member_id AND DATE(l.last_call_date) = ?
                GROUP BY m.id, m.name
                ORDER BY m.name";
        $s = db()->prepare($sql);
        $s->execute([$date, $date]);
        $rows = $s->fetchAll();

        render('reports/daily', compact('rows','date') + ['title' => 'Daily Calling Report']);
    }
}
