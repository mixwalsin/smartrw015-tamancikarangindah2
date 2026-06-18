<?php

/**
 * Routes - Web
 * Smart RW015 Taman Cikarang Indah 2
 *
 * Format: $router->get('/path', 'ControllerClass@method')
 *         $router->post('/path', 'ControllerClass@method')
 */

declare(strict_types=1);

// ──────────────────────────────────────────
// Home / Landing
// ──────────────────────────────────────────
$router->get('/', 'HomeController@index');

// ──────────────────────────────────────────
// Autentikasi
// ──────────────────────────────────────────
$router->get('/auth/login', 'AuthController@login');
$router->post('/auth/login', 'AuthController@processLogin');
$router->get('/auth/logout', 'AuthController@logout');
$router->get('/auth/register', 'AuthController@register');
$router->post('/auth/register', 'AuthController@processRegister');

// ──────────────────────────────────────────
// Dashboard
// ──────────────────────────────────────────
$router->get('/dashboard', 'DashboardController@index');
$router->get('/dashboard/rw', 'DashboardController@rw');
$router->get('/dashboard/rt', 'DashboardController@rt');

// ──────────────────────────────────────────
// Penduduk (Administrasi)
// ──────────────────────────────────────────
$router->get('/penduduk', 'PendudukController@index');
$router->get('/penduduk/create', 'PendudukController@create');
$router->post('/penduduk/store', 'PendudukController@store');
$router->get('/penduduk/show/:id', 'PendudukController@show');
$router->get('/penduduk/edit/:id', 'PendudukController@edit');
$router->post('/penduduk/update/:id', 'PendudukController@update');
$router->post('/penduduk/delete/:id', 'PendudukController@delete');

// ──────────────────────────────────────────
// Surat Menyurat
// ──────────────────────────────────────────
$router->get('/surat', 'SuratController@index');
$router->get('/surat/create', 'SuratController@create');
$router->post('/surat/store', 'SuratController@store');
$router->get('/surat/show/:id', 'SuratController@show');
$router->post('/surat/approve/:id', 'SuratController@approve');
$router->post('/surat/reject/:id', 'SuratController@reject');
$router->get('/surat/print/:id', 'SuratController@print');

// ──────────────────────────────────────────
// Keuangan
// ──────────────────────────────────────────
$router->get('/keuangan', 'KeuanganController@index');
$router->get('/keuangan/create', 'KeuanganController@create');
$router->post('/keuangan/store', 'KeuanganController@store');
$router->get('/keuangan/show/:id', 'KeuanganController@show');

// ──────────────────────────────────────────
// Pengaduan
// ──────────────────────────────────────────
$router->get('/pengaduan', 'PengaduanController@index');
$router->get('/pengaduan/create', 'PengaduanController@create');
$router->post('/pengaduan/store', 'PengaduanController@store');
$router->get('/pengaduan/show/:id', 'PengaduanController@show');
$router->post('/pengaduan/update-status/:id', 'PengaduanController@updateStatus');

// ──────────────────────────────────────────
// Kegiatan / Event
// ──────────────────────────────────────────
$router->get('/kegiatan', 'KegiatanController@index');
$router->get('/kegiatan/create', 'KegiatanController@create');
$router->post('/kegiatan/store', 'KegiatanController@store');
$router->get('/kegiatan/show/:id', 'KegiatanController@show');
$router->get('/kegiatan/edit/:id', 'KegiatanController@edit');
$router->post('/kegiatan/update/:id', 'KegiatanController@update');
$router->post('/kegiatan/delete/:id', 'KegiatanController@delete');

// ──────────────────────────────────────────
// UMKM
// ──────────────────────────────────────────
$router->get('/umkm', 'UmkmController@index');
$router->get('/umkm/create', 'UmkmController@create');
$router->post('/umkm/store', 'UmkmController@store');
$router->get('/umkm/show/:id', 'UmkmController@show');
$router->get('/umkm/edit/:id', 'UmkmController@edit');
$router->post('/umkm/update/:id', 'UmkmController@update');
$router->post('/umkm/delete/:id', 'UmkmController@delete');

// ──────────────────────────────────────────
// Posyandu
// ──────────────────────────────────────────
$router->get('/posyandu', 'PosyanduController@index');
$router->get('/posyandu/create', 'PosyanduController@create');
$router->post('/posyandu/store', 'PosyanduController@store');
$router->get('/posyandu/show/:id', 'PosyanduController@show');

// ──────────────────────────────────────────
// Keamanan / Security
// ──────────────────────────────────────────
$router->get('/keamanan', 'KeamananController@index');
$router->get('/keamanan/create', 'KeamananController@create');
$router->post('/keamanan/store', 'KeamananController@store');

// ──────────────────────────────────────────
// Statistik
// ──────────────────────────────────────────
$router->get('/statistik', 'StatistikController@index');

// ──────────────────────────────────────────
// Profil Pengguna
// ──────────────────────────────────────────
$router->get('/profil', 'ProfilController@index');
$router->post('/profil/update', 'ProfilController@update');
$router->post('/profil/password', 'ProfilController@changePassword');

// ──────────────────────────────────────────
// Admin - Manajemen Pengguna
// ──────────────────────────────────────────
$router->get('/admin/users', 'Admin\UserController@index');
$router->get('/admin/users/create', 'Admin\UserController@create');
$router->post('/admin/users/store', 'Admin\UserController@store');
$router->get('/admin/users/edit/:id', 'Admin\UserController@edit');
$router->post('/admin/users/update/:id', 'Admin\UserController@update');
$router->post('/admin/users/delete/:id', 'Admin\UserController@delete');

// ──────────────────────────────────────────
// RBAC – Role Management
// ──────────────────────────────────────────
$router->get('/admin/roles', 'Admin\RoleController@index');
$router->get('/admin/roles/create', 'Admin\RoleController@create');
$router->post('/admin/roles/store', 'Admin\RoleController@store');
$router->get('/admin/roles/edit/:id', 'Admin\RoleController@edit');
$router->post('/admin/roles/update/:id', 'Admin\RoleController@update');
$router->post('/admin/roles/delete/:id', 'Admin\RoleController@delete');
$router->get('/admin/roles/permissions/:id', 'Admin\RoleController@permissions');
$router->post('/admin/roles/sync-permissions/:id', 'Admin\RoleController@syncPermissions');

// ──────────────────────────────────────────
// RBAC – Permission Management
// ──────────────────────────────────────────
$router->get('/admin/permissions', 'Admin\PermissionController@index');
$router->get('/admin/permissions/create', 'Admin\PermissionController@create');
$router->post('/admin/permissions/store', 'Admin\PermissionController@store');
$router->get('/admin/permissions/edit/:id', 'Admin\PermissionController@edit');
$router->post('/admin/permissions/update/:id', 'Admin\PermissionController@update');
$router->post('/admin/permissions/delete/:id', 'Admin\PermissionController@delete');

// ──────────────────────────────────────────
// RBAC – Assign Role ke User
// ──────────────────────────────────────────
$router->get('/admin/user-roles', 'Admin\UserRoleController@index');
$router->post('/admin/user-roles/assign/:id', 'Admin\UserRoleController@assign');

// ──────────────────────────────────────────
// RBAC – Audit Log
// ──────────────────────────────────────────
$router->get('/admin/audit-log', 'Admin\AuditLogController@index');

// ──────────────────────────────────────────
// API (JSON responses)
// ──────────────────────────────────────────
$router->get('/api/statistik', 'Api\StatistikController@index');
$router->get('/api/pengaduan', 'Api\PengaduanController@index');
