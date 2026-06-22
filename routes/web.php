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
// Posyandu - Dashboard
// ──────────────────────────────────────────
$router->get('/posyandu', 'PosyanduController@index');

// ── Balita
$router->get('/posyandu/balita', 'PosyanduController@balitaIndex');
$router->get('/posyandu/balita/create', 'PosyanduController@balitaCreate');
$router->post('/posyandu/balita/store', 'PosyanduController@balitaStore');
$router->get('/posyandu/balita/show/:id', 'PosyanduController@balitaShow');
$router->get('/posyandu/balita/edit/:id', 'PosyanduController@balitaEdit');
$router->post('/posyandu/balita/update/:id', 'PosyanduController@balitaUpdate');
$router->post('/posyandu/balita/delete/:id', 'PosyanduController@balitaDelete');

// ── Ibu Hamil
$router->get('/posyandu/ibu-hamil', 'PosyanduController@ibuHamilIndex');
$router->get('/posyandu/ibu-hamil/create', 'PosyanduController@ibuHamilCreate');
$router->post('/posyandu/ibu-hamil/store', 'PosyanduController@ibuHamilStore');
$router->get('/posyandu/ibu-hamil/show/:id', 'PosyanduController@ibuHamilShow');
$router->get('/posyandu/ibu-hamil/edit/:id', 'PosyanduController@ibuHamilEdit');
$router->post('/posyandu/ibu-hamil/update/:id', 'PosyanduController@ibuHamilUpdate');
$router->post('/posyandu/ibu-hamil/delete/:id', 'PosyanduController@ibuHamilDelete');

// ── Jadwal Posyandu
$router->get('/posyandu/jadwal', 'PosyanduController@jadwalIndex');
$router->get('/posyandu/jadwal/create', 'PosyanduController@jadwalCreate');
$router->post('/posyandu/jadwal/store', 'PosyanduController@jadwalStore');
$router->get('/posyandu/jadwal/edit/:id', 'PosyanduController@jadwalEdit');
$router->post('/posyandu/jadwal/update/:id', 'PosyanduController@jadwalUpdate');
$router->post('/posyandu/jadwal/delete/:id', 'PosyanduController@jadwalDelete');

// ── Imunisasi
$router->get('/posyandu/imunisasi', 'PosyanduController@imunisasiIndex');
$router->get('/posyandu/imunisasi/create', 'PosyanduController@imunisasiCreate');
$router->post('/posyandu/imunisasi/store', 'PosyanduController@imunisasiStore');
$router->post('/posyandu/imunisasi/delete/:id', 'PosyanduController@imunisasiDelete');

// ── Timbangan
$router->get('/posyandu/timbangan', 'PosyanduController@timbanganIndex');
$router->get('/posyandu/timbangan/create', 'PosyanduController@timbanganCreate');
$router->post('/posyandu/timbangan/store', 'PosyanduController@timbanganStore');
$router->post('/posyandu/timbangan/delete/:id', 'PosyanduController@timbanganDelete');

// ── Grafik Pertumbuhan
$router->get('/posyandu/grafik', 'PosyanduController@grafikIndex');
$router->get('/posyandu/grafik/:balita_id', 'PosyanduController@grafikBalita');

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
// API (JSON responses)
// ──────────────────────────────────────────
$router->get('/api/statistik', 'Api\StatistikController@index');
$router->get('/api/pengaduan', 'Api\PengaduanController@index');
