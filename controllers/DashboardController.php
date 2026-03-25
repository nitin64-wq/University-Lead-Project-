<?php
class DashboardController {
    public function index(): void {
        requireAuth();
        
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'member') {
            $recent = LeadModel::paginate([], 1, 15)['data'];
            // Later we can filter by assigned_team_id if we store member team in session
            render('dashboard/telecaller', compact('recent') + ['title'=>'Telecaller Dashboard']);
            return;
        }

        $stats   = LeadModel::stats();
        $teams   = TeamModel::all();
        $members = MemberModel::all();
        $byDistrict = LeadModel::countBy('district');
        $bySchool   = LeadModel::countBy('school_name');
        $byCourse   = LeadModel::countBy('course_interested');
        $byStatus   = LeadModel::countBy('lead_status');
        $recent     = LeadModel::paginate([], 1, 8)['data'];
        render('dashboard/index', compact('stats','teams','members','byDistrict','bySchool','byCourse','byStatus','recent') + ['title'=>'Dashboard']);
    }
}
