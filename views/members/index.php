<div class="page-header">
    <div>
        <h4><i class="fas fa-user-friends me-2"></i>Member Management</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Home</a></li><li class="breadcrumb-item active">Members</li></ol></nav>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#memberModal"><i class="fas fa-plus me-1"></i>Add Member</button>
</div>

<!-- Filters -->
<div class="card dashboard-card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/members" class="row g-3">
            <div class="col-md-4">
                <select name="team_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Teams</option>
                    <?php foreach ($teams as $t): ?><option value="<?= $t['id'] ?>" <?= ($_GET['team_id']??'')==$t['id']?'selected':'' ?>><?= e($t['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="shift" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Shifts</option>
                    <?php foreach (['Morning','Evening','Night'] as $s): ?><option value="<?= $s ?>" <?= ($_GET['shift']??'')===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-1">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search members..." value="<?= e($_GET['search']??'') ?>">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card dashboard-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Team</th><th>Shift</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($members as $m): ?>
                <tr>
                    <td><strong><?= e($m['name']) ?></strong></td>
                    <td><?= e($m['email']) ?></td>
                    <td><?= e($m['phone']) ?></td>
                    <td><span class="badge bg-secondary"><?= e($m['role']) ?></span></td>
                    <td><?= $m['team_name'] ? '<span class="badge bg-primary">'.e($m['team_name']).'</span>' : '<span class="text-muted">No Team</span>' ?></td>
                    <td><span class="badge-shift <?=$m['shift']==='Morning'?'shift-morning':($m['shift']==='Evening'?'shift-evening':'shift-night')?>"><?= e($m['shift']) ?></span></td>
                    <td>
                        <button class="action-btn btn-edit text-success" title="Move" onclick="openMoveModal(<?= $m['id'] ?>,'<?= e($m['name']) ?>',<?= $m['team_id']?:'null' ?>)"><i class="fas fa-exchange-alt"></i></button>
                        <button class="action-btn btn-edit" title="Edit" onclick="openEditModal(<?= $m['id'] ?>,'<?= e($m['name']) ?>','<?= e($m['email']) ?>','<?= e($m['phone']) ?>','<?= e($m['role']) ?>',<?= $m['team_id']?:'null' ?>,'<?= e($m['shift']) ?>')"><i class="fas fa-edit"></i></button>
                        <a href="<?= BASE_URL ?>/members/<?= $m['id'] ?>/delete" class="action-btn btn-delete btn-delete-confirm" title="Delete"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; if(empty($members)): ?>
                <tr><td colspan="7"><div class="empty-state"><i class="fas fa-user-friends"></i><h6>No members found</h6></div></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add/Edit Member -->
<div class="modal fade" id="memberModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="memberModalTitle"><i class="fas fa-user-plus me-2"></i>Add Member</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form id="memberForm" method="POST" action="<?= BASE_URL ?>/members/store">
            <div class="modal-body row g-3">
                <div class="col-md-6"><label class="form-label">Full Name *</label><input type="text" name="name" id="mName" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Email Context *</label><input type="email" name="email" id="mEmail" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" id="mPhone" class="form-control"></div>
                <div class="col-md-6"><label class="form-label text-danger">Password * <small>(Leave blank to keep current on edit)</small></label><input type="password" name="password" id="mPass" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Role</label>
                    <select name="role" id="mRole" class="form-select">
                        <option value="Telecaller">Telecaller</option>
                        <option value="Team Lead">Team Lead</option>
                        <option value="Manager">Manager</option>
                    </select>
                </div>
                <div class="col-md-4"><label class="form-label">Assign Team</label>
                    <select name="team_id" id="mTeam" class="form-select">
                        <option value="">No Team</option>
                        <?php foreach($teams as $t): ?><option value="<?= $t['id'] ?>"><?= e($t['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4"><label class="form-label">Shift</label>
                    <select name="shift" id="mShift" class="form-select">
                        <option value="Morning">Morning</option>
                        <option value="Evening">Evening</option>
                        <option value="Night">Night</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Member</button>
            </div>
        </form>
    </div></div>
</div>

<!-- Modal: Move Member -->
<div class="modal fade" id="moveModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-success text-white">
            <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i>Move <span id="moveNameDisplay"></span></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form id="moveForm" method="POST" action="">
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Select New Team</label>
                    <select name="team_id" id="moveTeamSelect" class="form-select" required>
                        <option value="">-- Remove from Team --</option>
                        <?php foreach($teams as $t): ?><option value="<?= $t['id'] ?>"><?= e($t['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i>Confirm Move</button>
            </div>
        </form>
    </div></div>
</div>

<script>
function openEditModal(id, name, email, phone, role, team_id, shift) {
    document.getElementById('memberModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Member';
    document.getElementById('memberForm').action = '<?= BASE_URL ?>/members/' + id + '/update';
    document.getElementById('mName').value = name;
    document.getElementById('mEmail').value = email;
    document.getElementById('mPhone').value = phone;
    document.getElementById('mRole').value = role;
    document.getElementById('mTeam').value = team_id || '';
    document.getElementById('mShift').value = shift;
    document.getElementById('mPass').required = false;
    new bootstrap.Modal(document.getElementById('memberModal')).show();
}
function document_ready() {
    document.getElementById('memberModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('memberModalTitle').innerHTML = '<i class="fas fa-user-plus me-2"></i>Add Member';
        document.getElementById('memberForm').action = '<?= BASE_URL ?>/members/store';
        document.getElementById('memberForm').reset();
        document.getElementById('mPass').required = true;
    });
}
document.addEventListener('DOMContentLoaded', document_ready);

function openMoveModal(id, name, team_id) {
    document.getElementById('moveNameDisplay').innerText = name;
    document.getElementById('moveForm').action = '<?= BASE_URL ?>/members/' + id + '/move';
    document.getElementById('moveTeamSelect').value = team_id || '';
    new bootstrap.Modal(document.getElementById('moveModal')).show();
}
</script>
