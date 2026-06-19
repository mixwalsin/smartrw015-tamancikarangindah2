<?php

/**
 * AuthController
 * Smart RW015 Taman Cikarang Indah 2
 */

declare(strict_types=1);

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/UserModel.php';

class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login(): void
    {
        if (isLoggedIn()) {
            $this->redirect('dashboard');
        }
        $this->view('auth/login', [
            'title' => 'Login - ' . APP_NAME,
        ], null);
    }

    public function processLogin(): void
    {
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid. Silakan coba lagi.');
            $this->redirect('auth/login');
        }

        $username = trim((string) $this->input('username', ''));
        $password = (string) $this->input('password', '');

        if ($username === '' || $password === '') {
            setFlash('error', 'Username dan password wajib diisi.');
            $this->redirect('auth/login');
        }

        $user = $this->userModel->findAuthUserByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            setFlash('error', 'Username atau password salah.');
            $this->redirect('auth/login');
        }

        if ((int) $user['is_active'] === 0) {
            setFlash('error', 'Akun Anda belum aktif. Hubungi administrator.');
            $this->redirect('auth/login');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'          => (int) $user['id'],
            'warga_id'    => $user['warga_id'] !== null ? (int) $user['warga_id'] : null,
            'name'        => $user['name'],
            'username'    => $user['username'],
            'email'       => $user['email'],
            'role'        => $user['role_slug'],
            'role_name'   => $user['role_name'],
            'permissions' => $this->userModel->getPermissions((int) $user['id'], (int) $user['role_id']),
        ];

        $this->userModel->updateLastLogin((int) $user['id']);
        logActivity('login', 'auth', (int) $user['id'], 'Pengguna berhasil login');

        setFlash('success', 'Selamat datang, ' . e($user['name']) . '!');
        $this->redirect('dashboard');
    }

    public function logout(): void
    {
        logActivity('logout', 'auth', authUser()['id'] ?? null, 'Pengguna logout');
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
        $this->redirect('auth/login');
    }

    public function register(): void
    {
        $this->view('auth/register', [
            'title' => 'Daftar Akun - ' . APP_NAME,
        ], null);
    }

    public function processRegister(): void
    {
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('auth/register');
        }

        $name     = trim((string) $this->input('name', ''));
        $username = trim((string) $this->input('username', ''));
        $email    = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');
        $confirm  = (string) $this->input('password_confirmation', '');

        if ($name === '' || $username === '' || $email === '' || $password === '') {
            setFlash('error', 'Semua kolom wajib diisi.');
            $this->redirect('auth/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Format email tidak valid.');
            $this->redirect('auth/register');
        }

        if ($password !== $confirm) {
            setFlash('error', 'Konfirmasi password tidak cocok.');
            $this->redirect('auth/register');
        }

        if (strlen($password) < 8) {
            setFlash('error', 'Password minimal 8 karakter.');
            $this->redirect('auth/register');
        }

        if ($this->userModel->findByUsername($username)) {
            setFlash('error', 'Username sudah digunakan.');
            $this->redirect('auth/register');
        }

        if ($this->userModel->findByEmail($email)) {
            setFlash('error', 'Email sudah terdaftar.');
            $this->redirect('auth/register');
        }

        $id = $this->userModel->registerWarga([
            'name'      => $name,
            'username'  => $username,
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_BCRYPT),
            'is_active' => 0,
        ]);

        logActivity('register', 'users', (int) $id, 'Registrasi akun warga baru');
        setFlash('success', 'Pendaftaran berhasil. Tunggu aktivasi dari administrator.');
        $this->redirect('auth/login');
    }
}
