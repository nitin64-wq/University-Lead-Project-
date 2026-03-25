<?php
class LeadController {
    public function index(): void {
        requireAuth();
        $filters = [
            'school'   => $_GET['school']   ?? '',
            'district' => $_GET['district'] ?? '',
            'course'   => $_GET['course']   ?? '',
            'temp'     => $_GET['temp']     ?? '',
            'status'   => $_GET['status']   ?? '',
            'search'   => $_GET['search']   ?? '',
        ];
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $result = LeadModel::paginate($filters, $page);
        $schools   = LeadModel::distinctValues('school_name');
        $districts = LeadModel::distinctValues('district');
        $courses   = LeadModel::distinctValues('course_interested');
        $teams     = TeamModel::all();
        render('leads/index', compact('result','filters','schools','districts','courses','teams') + ['title'=>'Leads List']);
    }

    public function show(int $id): void {
        requireAuth();
        $lead = LeadModel::find($id);
        if (!$lead) {
            flash('Lead not found.', 'danger');
            redirect('/leads');
        }
        // Fetch call logs so admin sees telecaller updates
        $logs = CallLogModel::forLead($id);
        render('leads/show', compact('lead', 'logs') + ['title' => 'Lead Details']);
    }

    public function create(): void {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            LeadModel::create($_POST);
            flash('Lead added successfully.', 'success');
            redirect('/leads');
        }
        $teams = TeamModel::all();
        render('leads/form', ['lead' => [], 'teams' => $teams, 'title' => 'Add Lead']);
    }

    public function edit(int $id): void {
        requireAuth();
        $lead = LeadModel::find($id);
        if (!$lead) { flash('Lead not found.', 'danger'); redirect('/leads'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            LeadModel::update($id, $_POST);
            flash('Lead updated successfully.', 'success');
            redirect('/leads');
        }
        $teams = TeamModel::all();
        render('leads/form', compact('lead','teams') + ['title'=>'Edit Lead']);
    }

    public function delete(int $id): void {
        requireAuth();
        LeadModel::delete($id);
        flash('Lead deleted.', 'success');
        redirect('/leads');
    }

    public function import(): void {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processImport();
            return;
        }
        render('leads/import', ['title' => 'Import Leads']);
    }

    private function processImport(): void {
        if (empty($_FILES['excel_file']['name'])) {
            flash('Please select a file.', 'danger');
            redirect('/leads/import');
        }
        $file    = $_FILES['excel_file'];
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['xlsx','xls','csv'])) {
            flash('Only .xlsx, .xls, .csv files are allowed.', 'danger');
            redirect('/leads/import');
        }
        $tmpPath = UPLOAD_DIR . uniqid('import_') . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $tmpPath)) {
            flash('File upload failed.', 'danger');
            redirect('/leads/import');
        }

        try {
            $reader    = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($tmpPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($tmpPath);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true);
        } catch (Exception $e) {
            @unlink($tmpPath);
            flash('Could not read file: ' . $e->getMessage(), 'danger');
            redirect('/leads/import');
        }
        @unlink($tmpPath);

        if (count($rows) < 2) {
            flash('No data found in file.', 'warning');
            redirect('/leads/import');
        }

        // Build header map
        $headers = array_map(fn($h) => strtolower(trim((string)$h)), array_shift($rows));
        $colMap = [
            'student_name'      => ['student name','student_name','name'],
            'father_name'       => ['father name','father_name','father'],
            'student_contact'   => ['student contact','student_contact','contact'],
            'parent_contact'    => ['parent contact','parent_contact'],
            'stream'            => ['stream'],
            'category'          => ['category'],
            'school_name'       => ['school name','school_name','school'],
            'district'          => ['district'],
            'village'           => ['village'],
            'course_interested' => ['course interested','course_interested','course'],
            'telecaller_name'   => ['telecaller name','telecaller_name','telecaller'],
            'call_duration'     => ['call duration','call_duration'],
            'call_type'         => ['call type','call_type'],
            'availability_date' => ['availability date','availability_date'],
            'lead_status'       => ['lead status','lead_status','status'],
            'temperature'       => ['temperature','temp','warm / hot / cold','warm/hot/cold'],
            'warm_level'        => ['warm level','warm_level'],
            'next_follow_up'    => ['next follow up','next_follow_up','follow up date'],
            'remarks'           => ['remarks','remark','comment'],
            'admission_status'  => ['admission status','admission_status'],
        ];

        // Map header index → field name
        $map = [];
        foreach ($headers as $idx => $h) {
            foreach ($colMap as $field => $aliases) {
                if (in_array($h, $aliases) && !isset($map[$field])) {
                    $map[$field] = $idx;
                }
            }
        }

        $imported = 0; $duplicates = 0; $errors = 0;
        foreach ($rows as $row) {
            try {
                $get = fn($f) => isset($map[$f]) ? trim((string)($row[$map[$f]] ?? '')) : '';
                $name    = $get('student_name');
                $contact = $get('student_contact');
                if (!$name && !$contact) { $errors++; continue; }
                if (LeadModel::isDuplicate($name, $contact)) { $duplicates++; continue; }
                LeadModel::create([
                    'student_name'      => $name,
                    'father_name'       => $get('father_name'),
                    'student_contact'   => $contact,
                    'parent_contact'    => $get('parent_contact'),
                    'stream'            => $get('stream'),
                    'category'          => $get('category'),
                    'school_name'       => $get('school_name'),
                    'district'          => $get('district'),
                    'village'           => $get('village'),
                    'course_interested' => $get('course_interested'),
                    'telecaller_name'   => $get('telecaller_name'),
                    'call_duration'     => $get('call_duration'),
                    'call_type'         => $get('call_type') ?: 'Fresh',
                    'availability_date' => $get('availability_date'),
                    'lead_status'       => $get('lead_status') ?: 'New',
                    'temperature'       => $get('temperature') ?: 'Cold',
                    'warm_level'        => $get('warm_level'),
                    'next_follow_up'    => $get('next_follow_up'),
                    'remarks'           => $get('remarks'),
                    'admission_status'  => $get('admission_status') ?: 'Pending',
                ]);
                $imported++;
            } catch (Exception $e) { $errors++; }
        }

        $total = $imported + $duplicates + $errors;
        $_SESSION['import_summary'] = compact('total','imported','duplicates','errors');
        redirect('/leads/import');
    }
}
