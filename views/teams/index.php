<div class="page-header">
    <div>
        <h4><i class="fas fa-users-cog me-2"></i>Team Management</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard">Home</a></li><li class="breadcrumb-item active">Teams</li></ol></nav>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#teamModal"><i class="fas fa-plus me-1"></i>Create Team</button>
</div>

<div class="row g-4">
<?php
$teamColors = ['linear-gradient(135deg,#4f46e5,#3730a3)','linear-gradient(135deg,#0d9488,#0f766e)','linear-gradient(135deg,#8b5cf6,#6d28d9)','linear-gradient(135deg,#ef4444,#dc2626)','linear-gradient(135deg,#f59e0b,#d97706)','linear-gradient(135deg,#06b6d4,#0891b2)'];
foreach ($teams as $i => $team):
    $tm = array_filter($members, fn($m) => $m['team_id'] == $team['id']);
?>
<div class="col-xl-4 col-md-6">
    <div class="team-card">
        <div class="team-card-header" style="background:<?= $teamColors[$i % count($teamColors)] ?>">
            <h5><i class="fas fa-users me-2"></i><?= e($team['name']) ?></h5>
            <p><?= e($team['description'] ?: 'No description') ?></p>
        </div>
        <div class="team-card-body">
            <div class="team-stat"><div class="team-stat-icon bg-primary bg-opacity-10"><i class="fas fa-users text-primary"></i></div><div class="team-stat-info"><h6><?= $team['member_count'] ?></h6><small>Members</small></div></div>
        </div>
        <div class="team-card-actions">
            <button class="btn btn-sm btn-outline-primary" onclick="editTeam(<?= $team['id'] ?>,'<?= e($team['name']) ?>','<?= e($team['description']) ?>')"><i class="fas fa-edit me-1"></i>Edit</button>
            <a href="<?= BASE_URL ?>/teams/<?= $team['id'] ?>/delete" class="btn btn-sm btn-outline-danger btn-delete-confirm"><i class="fas fa-trash me-1"></i>Delete</a>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php if (empty($teams)): ?>
<div class="col-12"><div class="empty-state"><i class="fas fa-users"></i><h6>No teams yet</h6><p>Create your first team to get started.</p></div></div>
<?php endif; ?>
</div>

<!-- Create Team Modal -->
<div class="modal fade" id="teamModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="teamModalTitle"><i class="fas fa-users me-2"></i>Create Team</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form id="teamForm" method="POST" action="<?= BASE_URL ?>/teams/store">
            <input type="hidden" name="_team_id" id="teamFormId">
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Team Name *</label><input type="text" name="name" id="teamFormName" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" id="teamFormDesc" class="form-control" rows="3"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
            </div>
        </form>
    </div></div>
</div>
<script>
function editTeam(id, name, desc) {
    document.getElementById('teamModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Team';
    document.getElementById('teamFormId').value = id;
    document.getElementById('teamFormName').value = name;
    document.getElementById('teamFormDesc').value = desc;
    document.getElementById('teamForm').action = '<?= BASE_URL ?>/teams/' + id + '/update';
    new bootstrap.Modal(document.getElementById('teamModal')).show();
}
</script>
