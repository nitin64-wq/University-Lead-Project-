<?php
class AuthController {
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            // Check admin first
            $admin = AdminModel::findByEmail($email);
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email']= $admin['email'];
                $_SESSION['user_type']  = 'admin';
                redirect('/dashboard');
            }
            
            // Check member
            $member = MemberModel::findByEmail($email);
            if ($member && password_verify($password, $member['password'])) {
                $_SESSION['member_id']     = $member['id'];
                $_SESSION['member_name']   = $member['name'];
                $_SESSION['member_email']  = $member['email'];
                $_SESSION['member_role']   = $member['role'];
                $_SESSION['member_team_id']= $member['team_id'];
                $_SESSION['user_type']     = 'member';
                redirect('/tc/dashboard');
            }
            
            $error = 'Invalid email or password.';
        }
        render('auth/login', ['error' => $error ?? null, 'title' => 'Login']);
    }

    public function logout(): void {
        session_destroy();
        redirect('/login');
    }
}
