<?php $isEdit = !empty($lead['id']); $action = $isEdit ? BASE_URL.'/leads/'.$lead['id'].'/edit' : BASE_URL.'/leads/create'; ?>
<div class="page-header">
    <div>
        <h4><i class="fas fa-<?= $isEdit?'edit':'user-plus' ?> me-2"></i><?= $isEdit?'Edit':'Add' ?> Lead</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/leads">Leads</a></li><li class="breadcrumb-item active"><?= $isEdit?'Edit':'Add' ?></li></ol></nav>
    </div>
</div>
<div class="card dashboard-card">
    <div class="card-body">
        <form method="POST" action="<?= $action ?>">
            <div class="row g-3">
                <?php
                $fields = [
                    ['student_name','Student Name','text',true],['father_name','Father Name','text',false],
                    ['student_contact','Student Contact','text',true],['parent_contact','Parent Contact','text',false],
                    ['stream','Stream','text',false],['category','Category','text',false],
                    ['school_name','School Name','text',true],['district','District','text',true],
                    ['village','Village','text',false],['course_interested','Course Interested','text',true],
                    ['telecaller_name','Telecaller Name','text',false],['call_duration','Call Duration','text',false],
                ];
                foreach ($fields as [$name,$label,$type,$req]):
                ?>
                <div class="col-md-4">
                    <label class="form-label"><?= $label ?><?= $req?' *':'' ?></label>
                    <input type="<?= $type ?>" class="form-control" name="<?= $name ?>" value="<?= e($lead[$name]??'') ?>" <?= $req?'required':'' ?>>
                </div>
                <?php endforeach; ?>
                <div class="col-md-4">
                    <label class="form-label">Call Type</label>
                    <select name="call_type" class="form-select">
                        <?php foreach(['Fresh','Follow Up'] as $v): ?><option value="<?= $v ?>" <?= ($lead['call_type']??'')===$v?'selected':'' ?>><?= $v ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Availability Date</label>
                    <input type="date" name="availability_date" class="form-control" value="<?= e($lead['availability_date']??'') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lead Status</label>
                    <select name="lead_status" class="form-select">
                        <?php foreach(['New','Contacted','Interested','Follow Up','Converted','Not Interested'] as $v): ?><option value="<?= $v ?>" <?= ($lead['lead_status']??'New')===$v?'selected':'' ?>><?= $v ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Temperature</label>
                    <select name="temperature" class="form-select">
                        <?php foreach(['Hot','Warm','Cold'] as $v): ?><option value="<?= $v ?>" <?= ($lead['temperature']??'Cold')===$v?'selected':'' ?>><?= $v ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Warm Level</label>
                    <input type="text" name="warm_level" class="form-control" value="<?= e($lead['warm_level']??'') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Next Follow Up</label>
                    <input type="date" name="next_follow_up" class="form-control" value="<?= e($lead['next_follow_up']??'') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Admission Status</label>
                    <select name="admission_status" class="form-select">
                        <?php foreach(['Pending','Done','Cancelled'] as $v): ?><option value="<?= $v ?>" <?= ($lead['admission_status']??'Pending')===$v?'selected':'' ?>><?= $v ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2"><?= e($lead['remarks']??'') ?></textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Lead</button>
                <a href="<?= BASE_URL ?>/leads" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
