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

        $username = trim($this->input('username', ''));
        $password = $this->input('password', '');

        if ($username === '' || $password === '') {
            setFlash('error', 'Username dan password wajib diisi.');
            $this->redirect('auth/login');
        }

        $user = $this->userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            setFlash('error', 'Username atau password salah.');
            $this->redirect('auth/login');
        }

        if ((int) $user['is_active'] === 0) {
            setFlash('error', 'Akun Anda belum aktif. Hubungi administrator.');
            $this->redirect('auth/login');
        }

        // Set session
        $_SESSION['user'] = [
            'id'       => $user['id'],
            'name'     => $user['name'],
            'username' => $user['username'],
            'email'    => $user['email'],
            'role'     => $user['role'] ?? $user['slug'] ?? '',
            'role_id'  => $user['role_id'] ?? null,
            'warga_id' => $user['warga_id'] ?? null,
        ];

        if (!empty($user['role_name']) && empty($_SESSION['user']['role'])) {
            $_SESSION['user']['role'] = $user['role_name'];
        }

        $this->userModel->updateLastLogin((int) $user['id']);

        setFlash('success', 'Selamat datang, ' . e($user['name']) . '!');
        $this->redirect('dashboard');
    }

    public function logout(): void
    {
        $_SESSION = [];
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

        $name     = trim($this->input('name', ''));
        $username = trim($this->input('username', ''));
        $email    = trim($this->input('email', ''));
        $password = $this->input('password', '');
        $confirm  = $this->input('password_confirmation', '');

        if ($name === '' || $username === '' || $email === '' || $password === '') {
            setFlash('error', 'Semua kolom wajib diisi.');
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

        $this->userModel->insert([
            'name'      => $name,
            'username'  => $username,
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_BCRYPT),
            'role'      => 'warga',
            'is_active' => 0, // Perlu aktivasi admin
        ]);

        setFlash('success', 'Pendaftaran berhasil. Tunggu aktivasi dari administrator.');
        $this->redirect('auth/login');
    }
}
