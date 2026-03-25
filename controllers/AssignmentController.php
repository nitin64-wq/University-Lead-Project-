<?php
class AssignmentController {
    public function index(): void {
        requireAuth();
        $filters = [
            'school'   => $_GET['school']   ?? '',
            'district' => $_GET['district'] ?? '',
            'course'   => $_GET['course']   ?? '',
            'status'   => $_GET['status']   ?? '',
        ];
        $leads     = LeadModel::all($filters);
        $teams     = TeamModel::all();
        $schools   = LeadModel::distinctValues('school_name');
        $districts = LeadModel::distinctValues('district');
        $courses   = LeadModel::distinctValues('course_interested');
        render('assignment/index', compact('leads','teams','filters','schools','districts','courses') + ['title'=>'Lead Assignment']);
    }

    public function assign(): void {
        requireAuth();
        $leadIds = $_POST['lead_ids'] ?? [];
        $teamId  = (int)($_POST['team_id'] ?? 0);
        if (!$teamId || empty($leadIds)) { flash('Select at least one lead and a team.', 'warning'); redirect('/assignment'); return; }
        $count = LeadModel::assignTeam($leadIds, $teamId, $_SESSION['admin_id']);
        flash("$count leads assigned successfully.", 'success');
        redirect('/assignment');
    }

    public function assignBySchool(): void {
        requireAuth();
        $school = trim($_POST['school'] ?? '');
        $teamId = (int)($_POST['team_id'] ?? 0);
        if (!$school || !$teamId) { flash('Select school and team.', 'warning'); redirect('/assignment'); return; }
        $count = LeadModel::assignBySchool($school, $teamId, $_SESSION['admin_id']);
        flash("$count leads from \"$school\" assigned.", 'success');
        redirect('/assignment');
    }
}

class AnalyticsController {
    public function index(): void {
        requireAuth();
        $stats      = LeadModel::stats();
        $byDistrict = LeadModel::countBy('district');
        $bySchool   = LeadModel::countBy('school_name');
        $byCourse   = LeadModel::countBy('course_interested');
        $byStatus   = LeadModel::countBy('lead_status');
        $byTemp     = LeadModel::countBy('temperature');
        $teams      = TeamModel::all();
        render('analytics/index', compact('stats','byDistrict','bySchool','byCourse','byStatus','byTemp','teams') + ['title'=>'Analytics']);
    }
}
