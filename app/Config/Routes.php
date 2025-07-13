<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('dashboard', 'Dashboard::index');
 

$routes->get('/ajax/provinsi-by-wilayah/(:num)', 'Ajax::provinsiByWilayah/$1');
$routes->get('/ajax/area-by-wilayah/(:num)', 'Ajax::areaByWilayah/$1');
$routes->get('/ajax/kabupaten-by-provinsi/(:num)', 'Ajax::kabupatenByProvinsi/$1');
$routes->get('/ajax/kabupaten-by-area/(:num)', 'Ajax::kabupatenByArea/$1');
$routes->get('/ajax/jumlah-nozzle/(:num)', 'Ajax::jumlahNozzle/$1');

$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::loginProcess');
$routes->get('logout', 'Auth::logout');

$routes->get('user', 'Users::index');
$routes->get('user/create', 'Users::create');
$routes->post('user/store', 'Users::store');
$routes->get('user/edit/(:num)', 'Users::edit/$1');
$routes->post('user/update/(:num)', 'Users::update/$1');
$routes->post('user/delete/(:num)', 'Users::delete/$1');

$routes->get('wilayah', 'Wilayah::index');
$routes->get('wilayah/create', 'Wilayah::create');
$routes->post('wilayah/store', 'Wilayah::store');
$routes->get('wilayah/edit/(:num)', 'Wilayah::edit/$1');
$routes->post('wilayah/update/(:num)', 'Wilayah::update/$1');
$routes->get('wilayah/delete/(:num)', 'Wilayah::delete/$1');

$routes->get('area', 'Area::index');
$routes->get('area/create', 'Area::create');
$routes->post('area/store', 'Area::store');
$routes->get('area/edit/(:num)', 'Area::edit/$1');
$routes->post('area/update/(:num)', 'Area::update/$1');
$routes->get('area/delete/(:num)', 'Area::delete/$1');

$routes->get('/provinsi', 'Provinsi::index');
$routes->get('/provinsi/create', 'Provinsi::create');
$routes->post('/provinsi/store', 'Provinsi::store');
$routes->get('/provinsi/edit/(:num)', 'Provinsi::edit/$1');
$routes->post('/provinsi/update/(:num)', 'Provinsi::update/$1');
$routes->get('/provinsi/delete/(:num)', 'Provinsi::delete/$1');

$routes->get('/kabupaten', 'Kabupaten::index');
$routes->get('/kabupaten/create', 'Kabupaten::create');
$routes->post('/kabupaten/store', 'Kabupaten::store');
$routes->get('/kabupaten/edit/(:num)', 'Kabupaten::edit/$1');
$routes->post('/kabupaten/update/(:num)', 'Kabupaten::update/$1');
$routes->get('/kabupaten/delete/(:num)', 'Kabupaten::delete/$1');


$routes->get('/produk', 'Produk::index');
$routes->get('/produk/create', 'Produk::create');
$routes->post('/produk/store', 'Produk::store');
$routes->get('/produk/edit/(:num)', 'Produk::edit/$1');
$routes->post('/produk/update/(:num)', 'Produk::update/$1');
$routes->get('/produk/delete/(:num)', 'Produk::delete/$1');

$routes->get('/harga', 'Harga::index');
$routes->get('/harga/edit/(:num)', 'Harga::edit/$1');
$routes->post('/harga/update/(:num)', 'Harga::update/$1');
$routes->get('/harga/create', 'Harga::create');
$routes->post('/harga/store', 'Harga::store');
$routes->get('/harga/delete/(:num)', 'Harga::delete/$1');
$routes->get('/harga/log/(:num)', 'Harga::log/$1'); // log per produk


$routes->get('spbu', 'Spbu::index');
$routes->get('spbu/create', 'Spbu::create');
$routes->post('spbu/store', 'Spbu::store');
$routes->get('spbu/edit/(:num)', 'Spbu::edit/$1');
$routes->post('spbu/update/(:num)', 'Spbu::update/$1');
$routes->get('spbu/detail/(:num)', 'Spbu::detail/$1');
$routes->get('spbu/delete/(:num)', 'Spbu::delete/$1');
$routes->get('/spbu/import', 'Spbu::importForm');
$routes->post('/spbu/import', 'Spbu::importExcel');
$routes->get('/spbu/cetak/(:num)', 'Spbu::cetak/$1');

// Jenis SPBU
$routes->get('/jenis-spbu', 'JenisSpbu::index');
$routes->get('/jenis-spbu/create', 'JenisSpbu::create');
$routes->post('/jenis-spbu/store', 'JenisSpbu::store');
$routes->get('/jenis-spbu/edit/(:num)', 'JenisSpbu::edit/$1');
$routes->post('/jenis-spbu/update/(:num)', 'JenisSpbu::update/$1');
$routes->get('/jenis-spbu/delete/(:num)', 'JenisSpbu::delete/$1');

// Type SPBU
$routes->get('/type-spbu', 'TypeSpbu::index');
$routes->get('/type-spbu/create', 'TypeSpbu::create');
$routes->post('/type-spbu/store', 'TypeSpbu::store');
$routes->get('/type-spbu/edit/(:num)', 'TypeSpbu::edit/$1');
$routes->post('/type-spbu/update/(:num)', 'TypeSpbu::update/$1');
$routes->get('/type-spbu/delete/(:num)', 'TypeSpbu::delete/$1');

$routes->get('/kelas-spbu', 'KelasSpbu::index');
$routes->get('/kelas-spbu/create', 'KelasSpbu::create');
$routes->post('/kelas-spbu/store', 'KelasSpbu::store');
$routes->get('/kelas-spbu/edit/(:num)', 'KelasSpbu::edit/$1');
$routes->post('/kelas-spbu/update/(:num)', 'KelasSpbu::update/$1');
$routes->get('/kelas-spbu/delete/(:num)', 'KelasSpbu::delete/$1');


// Dispenser
$routes->get('dispenser', 'Dispenser::index');
$routes->get('dispenser/create', 'Dispenser::create');
$routes->post('dispenser/store', 'Dispenser::store');
$routes->get('dispenser/edit/(:num)', 'Dispenser::edit/$1');
$routes->post('dispenser/update/(:num)', 'Dispenser::update/$1');
$routes->get('dispenser/delete/(:num)', 'Dispenser::delete/$1');
$routes->get('dispenser/log/(:num)', 'Dispenser::log/$1');



// Nozzle
$routes->get('nozzle', 'Nozzle::index');
$routes->get('nozzle/create', 'Nozzle::create');
$routes->post('nozzle/store', 'Nozzle::store');
$routes->get('nozzle/edit/(:num)', 'Nozzle::edit/$1');
$routes->post('nozzle/update/(:num)', 'Nozzle::update/$1');
$routes->get('nozzle/delete/(:num)', 'Nozzle::delete/$1');
$routes->get('api/nozzles', 'NozzleController::getByDispenser');

// Tangki BBM
$routes->get('tangki', 'Tangki::index');
$routes->get('tangki/create', 'Tangki::create');
$routes->post('tangki/store', 'Tangki::store');
$routes->get('tangki/edit/(:num)', 'Tangki::edit/$1');
$routes->post('tangki/update/(:num)', 'Tangki::update/$1');
$routes->get('tangki/delete/(:num)', 'Tangki::delete/$1');

$routes->get('penerimaan', 'Penerimaan::index');
$routes->get('penerimaan/create', 'Penerimaan::create');
$routes->post('penerimaan/store', 'Penerimaan::store');
$routes->get('penerimaan/edit/(:num)', 'Penerimaan::edit/$1');
$routes->post('penerimaan/update/(:num)', 'Penerimaan::update/$1');
$routes->get('penerimaan/delete/(:num)', 'Penerimaan::delete/$1');
$routes->get('penerimaan/dipping/(:num)', 'Penerimaan::dipping/$1');
$routes->post('penerimaan/process-dipping/(:num)', 'Penerimaan::processDipping/$1');

$routes->get('/penjualan', 'Penjualan::index');
$routes->get('/penjualan/create', 'Penjualan::create');
$routes->post('/penjualan/store', 'Penjualan::store');
$routes->get('/penjualan/edit/(:num)', 'Penjualan::edit/$1');
$routes->post('/penjualan/update/(:num)', 'Penjualan::update/$1');
$routes->get('/penjualan/delete/(:num)', 'Penjualan::delete/$1');
$routes->get('penjualan/approve/(:num)', 'Penjualan::approve/$1');
$routes->get('penjualan/reject/(:num)', 'Penjualan::reject/$1');
$routes->get('penjualan/log/(:num)', 'Penjualan::log/$1');
$routes->get('penjualan/laporan', 'Penjualan::laporan');
$routes->get('penjualan/getMeter', 'Penjualan::getMeter');
$routes->post('penjualan/logReset', 'Penjualan::logReset');
$routes->post('penjualan/request-reset', 'Penjualan::requestReset');
$routes->get('penjualan/setup-initial-meters', 'Penjualan::setupInitialMeters');
$routes->post('penjualan/save-initial-meters', 'Penjualan::saveInitialMeters');
$routes->get('penjualan/reset-form', 'Penjualan::resetForm', ['filter' => 'auth']);
$routes->post('penjualan/submit-reset', 'Penjualan::submitReset', ['filter' => 'auth']);
$routes->post('penjualan/handle-reset', 'Penjualan::handleReset');
$routes->get('penjualan/reset-requests', 'Penjualan::resetRequests');
$routes->post('penjualan/approve-reset/(:num)', 'Penjualan::approveReset/$1');
$routes->get('penjualan/get-last-meter/(:num)', 'Penjualan::getLastMeter/$1');
$routes->get('laporan/penjualan', 'Penjualan::resetRequests');
$routes->get('penjualan/get-nozzles', 'Penjualan::getNozzles');


$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('approvals', 'AdminApprovalController::index');
    $routes->get('approvals/history', 'AdminApprovalController::history');
    
    $routes->get('approve-reset/(:num)', 'AdminApprovalController::approveReset/$1');
    $routes->get('reject-reset/(:num)', 'AdminApprovalController::rejectReset/$1');
});

// Gunakan hanya filter 'auth' saja
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('stok/input', 'Stok::inputStokReal');
    $routes->post('stok/simpan', 'Stok::simpanStokReal');
    $routes->get('stok/laporan', 'Stok::laporan');
    $routes->get('stok/audit', 'StokAudit::index');
    $routes->get('stok/audit/detail/(:num)', 'StokAudit::detail/$1');
    $routes->get('stok/initial', 'Stok::initialStok');
    $routes->post('stok/initial', 'Stok::initialStok');
});


$routes->get('kode-dispenser', 'KodeDispenser::index');
$routes->get('kode-dispenser/create', 'KodeDispenser::create');
$routes->post('kode-dispenser/store', 'KodeDispenser::store');
$routes->get('kode-dispenser/edit/(:num)', 'KodeDispenser::edit/$1');
$routes->post('kode-dispenser/update/(:num)', 'KodeDispenser::update/$1');
$routes->get('kode-dispenser/delete/(:num)', 'KodeDispenser::delete/$1');

// Nozzle
$routes->get('kode-nozzle', 'KodeNozzle::index');
$routes->get('kode-nozzle/create', 'KodeNozzle::create');
$routes->post('kode-nozzle/store', 'KodeNozzle::store');
$routes->get('kode-nozzle/edit/(:num)', 'KodeNozzle::edit/$1');
$routes->post('kode-nozzle/update/(:num)', 'KodeNozzle::update/$1');
$routes->get('kode-nozzle/delete/(:num)', 'KodeNozzle::delete/$1');


$routes->get('nozzle-test', 'NozzleTest::index');
$routes->get('nozzle-test/create', 'NozzleTest::create');
$routes->post('nozzle-test/store', 'NozzleTest::store');
$routes->get('nozzle-test/edit/(:num)', 'NozzleTest::edit/$1');
$routes->post('nozzle-test/update/(:num)', 'NozzleTest::update/$1');
$routes->get('nozzle-test/delete/(:num)', 'NozzleTest::delete/$1');
$routes->get('nozzle-test/get-nozzles', 'NozzleTest::getNozzles');
$routes->get('nozzle-test/get-penjualan', 'NozzleTest::getPenjualan');



$routes->get('operator', 'Operator::index');
$routes->get('operator/create', 'Operator::create');
$routes->post('operator/store', 'Operator::store');
$routes->get('operator/edit/(:num)', 'Operator::edit/$1');
$routes->post('operator/update/(:num)', 'Operator::update/$1');
$routes->get('operator/delete/(:num)', 'Operator::delete/$1');

$routes->group('perusahaan', function($routes) {
    $routes->get('/', 'Perusahaan::index');
    $routes->get('create', 'Perusahaan::create');
    $routes->post('save', 'Perusahaan::save');
    $routes->get('edit/(:num)', 'Perusahaan::edit/$1');
    $routes->post('update/(:num)', 'Perusahaan::update/$1');
    $routes->delete('delete/(:num)', 'Perusahaan::delete/$1');
    
});

$routes->post('perusahaan/getKabupatenByProvinsi', 'Perusahaan::getKabupatenByProvinsi');
$routes->get('unauthorized', 'ErrorController::unauthorized');



// API untuk get data
$routes->get('api/stok/last-stok/(:num)', 'Stok::getLastStok/$1'); // Get last stok by tangki_id



