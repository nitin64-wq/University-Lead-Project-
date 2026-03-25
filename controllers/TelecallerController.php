<?php
class TelecallerController {

    // ── Helper: enforce member-only access ──────────────────
    private function memberId(): int {
        if (empty($_SESSION['member_id'])) redirect('/login');
        return (int)$_SESSION['member_id'];
    }

    private function teamId(): ?int {
        $tid = $_SESSION['member_team_id'] ?? null;
        return $tid ? (int)$tid : null;
    }

    /** Build the base filter that scopes leads to this telecaller's visibility */
    private function tcFilter(): array {
        $mid = $this->memberId();
        $tid = $this->teamId();
        $f = [];
        if ($tid) {
            // Show leads assigned to their team OR directly to them
            $f['tc_team_id']   = $tid;
            $f['tc_member_id'] = $mid;
        } else {
            // No team — only individually-assigned leads
            $f['tc_member_id'] = $mid;
        }
        return $f;
    }

    // ── Dashboard ────────────────────────────────────────────
    public function dashboard(): void {
        $mid          = $this->memberId();
        $tid          = $this->teamId();
        $stats        = LeadModel::statsForMember($mid, $tid);
        $followups    = LeadModel::countFollowups($mid, $tid);
        $callStats    = CallLogModel::statsForMember($mid);
        $todayLeads   = LeadModel::paginate(array_merge($this->tcFilter(), ['followup_today' => 1]), 1, 5)['data'];
        $recentLogs   = CallLogModel::forMember($mid, 6);
        $notifications= NotificationModel::forMember($mid, 5);
        $unreadCount  = NotificationModel::countUnread($mid);
        $weekly       = CallLogModel::weeklyActivity($mid);
        render('telecaller/dashboard', compact(
            'stats','followups','callStats','todayLeads',
            'recentLogs','notifications','unreadCount','weekly'
        ) + ['title' => 'My Dashboard']);
    }

    // ── My Leads ─────────────────────────────────────────────
    public function leads(): void {
        $mid = $this->memberId();
        $filters = array_merge($this->tcFilter(), [
            'search'   => $_GET['search']   ?? '',
            'temp'     => $_GET['temp']     ?? '',
            'status'   => $_GET['status']   ?? '',
            'course'   => $_GET['course']   ?? '',
            'district' => $_GET['district'] ?? '',
        ]);
        $page      = max(1, (int)($_GET['page'] ?? 1));
        $result    = LeadModel::paginate($filters, $page);
        $courses   = LeadModel::distinctValues('course_interested');
        $districts = LeadModel::distinctValues('district');
        $unreadCount = NotificationModel::countUnread($mid);
        render('telecaller/leads', compact('result','filters','courses','districts','unreadCount') + ['title' => 'My Leads']);
    }

    // ── Follow-ups ───────────────────────────────────────────
    public function followups(): void {
        $mid = $this->memberId();
        $tid = $this->teamId();
        $tab = $_GET['tab'] ?? 'today';
        $filterKey = match($tab) {
            'upcoming' => 'followup_upcoming',
            'overdue'  => 'followup_overdue',
            default    => 'followup_today',
        };
        $result = LeadModel::paginate(array_merge($this->tcFilter(), [$filterKey => 1]), 1, 50);
        $counts = LeadModel::countFollowups($mid, $tid);
        $unreadCount = NotificationModel::countUnread($mid);
        render('telecaller/followups', compact('result','tab','counts','unreadCount') + ['title' => 'Follow-ups']);
    }

    // ── Lead Detail ──────────────────────────────────────────
    public function leadDetail(int $id): void {
        $mid  = $this->memberId();
        $tid  = $this->teamId();
        $lead = LeadModel::find($id);

        // Access check: lead must be assigned to member's team OR directly to them
        $teamMatch   = $tid && (int)($lead['assigned_team_id'] ?? 0) === $tid;
        $memberMatch = (int)($lead['assigned_member_id'] ?? 0) === $mid;

        if (!$lead || (!$teamMatch && !$memberMatch)) {
            flash('Lead not found or not assigned to you.', 'danger');
            redirect('/tc/leads');
        }

        $logs = CallLogModel::forLead($id);
        $unreadCount = NotificationModel::countUnread($mid);
        render('telecaller/lead_detail', compact('lead','logs','unreadCount') + ['title' => 'Lead Detail']);
    }

    // ── Log a Call (POST) ────────────────────────────────────
    public function logCall(int $leadId): void {
        $mid  = $this->memberId();
        $tid  = $this->teamId();
        $lead = LeadModel::find($leadId);

        $teamMatch   = $tid && (int)($lead['assigned_team_id'] ?? 0) === $tid;
        $memberMatch = (int)($lead['assigned_member_id'] ?? 0) === $mid;

        if (!$lead || (!$teamMatch && !$memberMatch)) {
            flash('Access denied.', 'danger'); redirect('/tc/leads');
        }

        $data = [
            'lead_id'         => $leadId,
            'telecaller_id'   => $mid,
            'call_duration'   => trim($_POST['call_duration']   ?? ''),
            'call_type'       => $_POST['call_type']        ?? 'Fresh',
            'lead_status'     => $_POST['lead_status']      ?? 'Contacted',
            'temperature'     => $_POST['temperature']      ?? 'Cold',
            'warm_level'      => $_POST['warm_level']       ?? '',
            'next_follow_up'  => $_POST['next_follow_up']   ?? '',
            'remarks'         => trim($_POST['remarks']      ?? ''),
            'admission_status'=> $_POST['admission_status'] ?? 'Pending',
        ];

        // Save call log
        CallLogModel::create($data);

        // Update lead record with latest call info
        LeadModel::updateCallInfo($leadId, array_merge($data, ['last_call_date' => date('Y-m-d')]));

        flash('Call logged successfully! 📞', 'success');
        redirect('/tc/leads/' . $leadId);
    }

    // ── Mark Notifications Read ──────────────────────────────
    public function markNotificationsRead(): void {
        NotificationModel::markAllRead($this->memberId());
        redirect('/tc/dashboard');
    }
}
