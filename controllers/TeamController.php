<?php
class TeamController {
    public function index(): void {
        requireAuth();
        $teams   = TeamModel::all();
        $members = MemberModel::all();
        render('teams/index', compact('teams','members') + ['title'=>'Team Management']);
    }
    public function store(): void {
        requireAuth();
        TeamModel::create($_POST);
        flash('Team created.', 'success');
        redirect('/teams');
    }
    public function update(int $id): void {
        requireAuth();
        TeamModel::update($id, $_POST);
        flash('Team updated.', 'success');
        redirect('/teams');
    }
    public function delete(int $id): void {
        requireAuth();
        TeamModel::delete($id);
        flash('Team deleted.', 'success');
        redirect('/teams');
    }
}

class MemberController {
    public function index(): void {
        requireAuth();
        $filters = [
            (int)($_GET['team_id'] ?? 0) ?: null,
            $_GET['shift'] ?? null,
            $_GET['search'] ?? null
        ];
        $members = MemberModel::all(...$filters);
        $teams   = TeamModel::all();
        render('members/index', compact('members','teams') + ['title'=>'Members']);
    }
    public function store(): void {
        requireAuth();
        if (MemberModel::findByEmail($_POST['email'] ?? '')) {
            flash('Email already exists.', 'danger'); redirect('/members'); return;
        }
        MemberModel::create($_POST);
        flash('Member added.', 'success');
        redirect('/members');
    }
    public function update(int $id): void {
        requireAuth();
        MemberModel::update($id, $_POST);
        flash('Member updated.', 'success');
        redirect('/members');
    }
    public function move(int $id): void {
        requireAuth();
        MemberModel::moveTo($id, (int)$_POST['team_id']);
        flash('Member moved.', 'success');
        redirect('/members');
    }
    public function delete(int $id): void {
        requireAuth();
        MemberModel::delete($id);
        flash('Member deleted.', 'success');
        redirect('/members');
    }
}
