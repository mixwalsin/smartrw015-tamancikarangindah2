<?php

declare(strict_types=1);

class ProfilController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        $this->requireAuth();
        $user = $this->userModel->find((int) (authUser()['id'] ?? 0));
        $this->view('profil/index', [
            'title' => 'Profil - ' . APP_NAME,
            'user' => $user,
        ]);
    }

    public function update(): void
    {
        $this->requireAuth();
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('profil');
        }

        $id = (int) (authUser()['id'] ?? 0);
        $this->userModel->update($id, [
            'name' => trim((string) $this->input('name', '')),
            'email' => trim((string) $this->input('email', '')),
        ]);
        $_SESSION['user']['name'] = trim((string) $this->input('name', authUser()['name'] ?? ''));
        $_SESSION['user']['email'] = trim((string) $this->input('email', authUser()['email'] ?? ''));
        logActivity('update', 'profil', $id, 'Memperbarui profil pengguna');
        setFlash('success', 'Profil berhasil diperbarui.');
        $this->redirect('profil');
    }

    public function changePassword(): void
    {
        $this->requireAuth();
        if (!verifyCsrf()) {
            setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('profil');
        }

        $id = (int) (authUser()['id'] ?? 0);
        $user = $this->userModel->find($id);
        $current = (string) $this->input('current_password', '');
        $password = (string) $this->input('password', '');
        $confirm = (string) $this->input('password_confirmation', '');

        if (!$user || !password_verify($current, $user['password'])) {
            setFlash('error', 'Password lama tidak sesuai.');
            $this->redirect('profil');
        }
        if ($password === '' || $password !== $confirm || strlen($password) < 8) {
            setFlash('error', 'Password baru minimal 8 karakter dan konfirmasi harus sama.');
            $this->redirect('profil');
        }

        $this->userModel->update($id, ['password' => password_hash($password, PASSWORD_BCRYPT)]);
        logActivity('update_password', 'profil', $id, 'Mengganti password akun');
        setFlash('success', 'Password berhasil diperbarui.');
        $this->redirect('profil');
    }
}
