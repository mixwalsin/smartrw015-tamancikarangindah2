<?php

declare(strict_types=1);

$router->get('/', 'HomeController@index');

$router->get('/auth/login', 'AuthController@login');
$router->post('/auth/login', 'AuthController@processLogin');
$router->get('/auth/logout', 'AuthController@logout');
$router->get('/auth/register', 'AuthController@register');
$router->post('/auth/register', 'AuthController@processRegister');

$router->get('/dashboard', 'DashboardController@index');
$router->get('/dashboard/rw', 'DashboardController@rw');
$router->get('/dashboard/rt', 'DashboardController@rt');

$router->get('/penduduk', 'PendudukController@index');
$router->get('/penduduk/create', 'PendudukController@create');
$router->post('/penduduk/store', 'PendudukController@store');
$router->get('/penduduk/show/:id', 'PendudukController@show');
$router->get('/penduduk/edit/:id', 'PendudukController@edit');
$router->post('/penduduk/update/:id', 'PendudukController@update');
$router->post('/penduduk/delete/:id', 'PendudukController@delete');

$router->get('/kk', 'KartuKeluargaController@index');
$router->get('/kk/create', 'KartuKeluargaController@create');
$router->post('/kk/store', 'KartuKeluargaController@store');
$router->get('/kk/show/:id', 'KartuKeluargaController@show');
$router->get('/kk/edit/:id', 'KartuKeluargaController@edit');
$router->post('/kk/update/:id', 'KartuKeluargaController@update');
$router->post('/kk/delete/:id', 'KartuKeluargaController@delete');

$router->get('/rumah', 'RumahController@index');
$router->get('/rumah/create', 'RumahController@create');
$router->post('/rumah/store', 'RumahController@store');
$router->get('/rumah/show/:id', 'RumahController@show');
$router->get('/rumah/edit/:id', 'RumahController@edit');
$router->post('/rumah/update/:id', 'RumahController@update');
$router->post('/rumah/delete/:id', 'RumahController@delete');

$router->get('/surat', 'SuratController@index');
$router->get('/surat/create', 'SuratController@create');
$router->post('/surat/store', 'SuratController@store');
$router->get('/surat/show/:id', 'SuratController@show');
$router->post('/surat/approve/:id', 'SuratController@approve');
$router->post('/surat/reject/:id', 'SuratController@reject');
$router->get('/surat/print/:id', 'SuratController@print');

$router->get('/keuangan', 'KeuanganController@index');
$router->get('/keuangan/create', 'KeuanganController@create');
$router->post('/keuangan/store', 'KeuanganController@store');
$router->get('/keuangan/show/:id', 'KeuanganController@show');

$router->get('/pengaduan', 'PengaduanController@index');
$router->get('/pengaduan/create', 'PengaduanController@create');
$router->post('/pengaduan/store', 'PengaduanController@store');
$router->get('/pengaduan/show/:id', 'PengaduanController@show');
$router->get('/pengaduan/edit/:id', 'PengaduanController@edit');
$router->post('/pengaduan/update/:id', 'PengaduanController@update');
$router->post('/pengaduan/delete/:id', 'PengaduanController@delete');
$router->post('/pengaduan/update-status/:id', 'PengaduanController@updateStatus');

$router->get('/kegiatan', 'KegiatanController@index');
$router->get('/kegiatan/create', 'KegiatanController@create');
$router->post('/kegiatan/store', 'KegiatanController@store');
$router->get('/kegiatan/show/:id', 'KegiatanController@show');
$router->get('/kegiatan/edit/:id', 'KegiatanController@edit');
$router->post('/kegiatan/update/:id', 'KegiatanController@update');
$router->post('/kegiatan/delete/:id', 'KegiatanController@delete');

$router->get('/umkm', 'UmkmController@index');
$router->get('/umkm/create', 'UmkmController@create');
$router->post('/umkm/store', 'UmkmController@store');
$router->get('/umkm/show/:id', 'UmkmController@show');
$router->get('/umkm/edit/:id', 'UmkmController@edit');
$router->post('/umkm/update/:id', 'UmkmController@update');
$router->post('/umkm/delete/:id', 'UmkmController@delete');

$router->get('/posyandu', 'PosyanduController@index');
$router->get('/posyandu/create', 'PosyanduController@create');
$router->post('/posyandu/store', 'PosyanduController@store');
$router->get('/posyandu/show/:id', 'PosyanduController@show');
$router->get('/posyandu/edit/:id', 'PosyanduController@edit');
$router->post('/posyandu/update/:id', 'PosyanduController@update');
$router->post('/posyandu/delete/:id', 'PosyanduController@delete');

$router->get('/keamanan', 'KeamananController@index');
$router->get('/keamanan/create', 'KeamananController@create');
$router->post('/keamanan/store', 'KeamananController@store');
$router->get('/keamanan/show/:id', 'KeamananController@show');
$router->get('/keamanan/edit/:id', 'KeamananController@edit');
$router->post('/keamanan/update/:id', 'KeamananController@update');
$router->post('/keamanan/delete/:id', 'KeamananController@delete');

$router->get('/pengumuman', 'PengumumanController@index');
$router->get('/pengumuman/create', 'PengumumanController@create');
$router->post('/pengumuman/store', 'PengumumanController@store');
$router->get('/pengumuman/show/:id', 'PengumumanController@show');
$router->get('/pengumuman/edit/:id', 'PengumumanController@edit');
$router->post('/pengumuman/update/:id', 'PengumumanController@update');
$router->post('/pengumuman/delete/:id', 'PengumumanController@delete');

$router->get('/notifikasi', 'NotifikasiController@index');
$router->post('/notifikasi/read/:id', 'NotifikasiController@markRead');

$router->get('/laporan', 'LaporanController@index');
$router->get('/laporan/export', 'LaporanController@export');
$router->get('/audit-log', 'AuditLogController@index');
$router->get('/audit-log/show/:id', 'AuditLogController@show');

$router->get('/statistik', 'StatistikController@index');

$router->get('/profil', 'ProfilController@index');
$router->post('/profil/update', 'ProfilController@update');
$router->post('/profil/password', 'ProfilController@changePassword');

$router->get('/admin/users', 'Admin\UserController@index');
$router->get('/admin/users/create', 'Admin\UserController@create');
$router->post('/admin/users/store', 'Admin\UserController@store');
$router->get('/admin/users/show/:id', 'Admin\UserController@show');
$router->get('/admin/users/edit/:id', 'Admin\UserController@edit');
$router->post('/admin/users/update/:id', 'Admin\UserController@update');
$router->post('/admin/users/delete/:id', 'Admin\UserController@delete');

$router->get('/api/statistik', 'Api\StatistikController@index');
$router->get('/api/pengaduan', 'Api\PengaduanController@index');
$router->post('/api/notifications/whatsapp/pengumuman', 'WhatsappNotificationController@announcement');
$router->post('/api/notifications/whatsapp/surat', 'WhatsappNotificationController@suratStatus');
