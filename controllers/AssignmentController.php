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
        $members   = MemberModel::all();
        $schools   = LeadModel::distinctValues('school_name');
        $districts = LeadModel::distinctValues('district');
        $courses   = LeadModel::distinctValues('course_interested');
        render('assignment/index', compact('leads','members','filters','schools','districts','courses') + ['title'=>'Lead Assignment']);
    }

    public function assign(): void {
        requireAuth();
        $leadIds = $_POST['lead_ids'] ?? [];
        $memberId = (int)($_POST['member_id'] ?? 0);
        if (!$memberId || empty($leadIds)) { flash('Select at least one lead and a telecaller.', 'warning'); redirect('/assignment'); return; }
        $count = LeadModel::assignMember($leadIds, $memberId, $_SESSION['admin_id']);
        flash("$count leads assigned successfully.", 'success');
        redirect('/assignment');
    }

    public function assignBySchool(): void {
        requireAuth();
        $school = trim($_POST['school'] ?? '');
        $memberId = (int)($_POST['member_id'] ?? 0);
        if (!$school || !$memberId) { flash('Select school and telecaller.', 'warning'); redirect('/assignment'); return; }
        $count = LeadModel::assignBySchool($school, $memberId, $_SESSION['admin_id']);
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
