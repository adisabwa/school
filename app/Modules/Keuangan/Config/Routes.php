<?php

use Modules\Keuangan\Controllers\Admin\KeuanganController;
use Modules\Keuangan\Controllers\Admin\Data\KasController;
use Modules\Keuangan\Controllers\Admin\Data\PosController;
use Modules\Keuangan\Controllers\Admin\Data\MetodeController;
use Modules\Keuangan\Controllers\Admin\Data\KategoriController;
use Modules\Keuangan\Controllers\Admin\Data\IuranController;
use Modules\Keuangan\Controllers\Admin\IuranSantriController;
use Modules\Keuangan\Controllers\Admin\PembayaranController;
use Modules\Keuangan\Controllers\Admin\TransaksiController;
use Modules\Keuangan\Controllers\Admin\IuranSaldoController;
use Modules\Keuangan\Controllers\Admin\AlokasiTransaksiController;

helper('route');

$routes->group('keuangan/admin', [
    'filter' => 'api-auth:rapor.admin',
], static function ($routes) {    
    
    //-------------------------------------Data Transaksi Keuangan -----------------------------------------------
    $routes->group('/', static function ($routes) {
        
        //-------------------------------------Data iuran -----------------------------------------------
        $routes->group('iuran', static function ($routes) {
            $routes->group('tagihan', static function ($routes) {
                addDefaultRoutes($routes, IuranSantriController::class, PREFIX_TABLE.'keu_iuran_santri');
                $routes->add('generate', [IuranSantriController::class, 'generate']);
                $routes->add('get_all_grouping', [IuranSantriController::class, 'get_all_grouping']);
            });
            $routes->group('pembayaran', static function ($routes) {
                addDefaultRoutes($routes, PembayaranController::class, PREFIX_TABLE.'keu_pembayaran_santri');
            });
        });

        //-------------------------------------Data Transaksi -----------------------------------------------
        $routes->group('transaksi', static function ($routes) {
            addDefaultRoutes($routes, TransaksiController::class, PREFIX_TABLE.'keu_transaksi');
            $routes->add('summary', [TransaksiController::class, 'summary']);
            $routes->add('download', [TransaksiController::class, 'download']);
        });

        //-------------------------------------Data Saldo -----------------------------------------------
        $routes->group('iuran-saldo', static function ($routes) {
            addDefaultRoutes($routes, IuranSaldoController::class, PREFIX_TABLE.'keu_saldo_iuran');
            $routes->add('get_saldo', [IuranSaldoController::class, 'get_saldo']);
        });

        //-------------------------------------Data Alokasi Transaksi -----------------------------------------------
        $routes->group('alokasi-transaksi', static function ($routes) {
            addDefaultRoutes($routes, AlokasiTransaksiController::class, PREFIX_TABLE.'keu_alokasi_transaksi');
        });
    });

    //-------------------------------------Data Master Keuangan -----------------------------------------------
    $routes->group('data', static function ($routes) {

        //-------------------------------------Data Kas Keuangan -----------------------------------------------
        $routes->group('kas', static function ($routes) {
            addDefaultRoutes($routes, KasController::class, PREFIX_TABLE.'keu_kas');
        });

        //-------------------------------------Data Pos Keuangan -----------------------------------------------
        $routes->group('pos', static function ($routes) {
            addDefaultRoutes($routes, PosController::class, PREFIX_TABLE.'keu_pos');
        });

        //-------------------------------------Data Metode Pembayaran -----------------------------------------------
        $routes->group('metode', static function ($routes) {
            addDefaultRoutes($routes, MetodeController::class, PREFIX_TABLE.'keu_metode');
        });

        //-------------------------------------Data Kategori Transaksi  -----------------------------------------------
        $routes->group('kategori', static function ($routes) {
            addDefaultRoutes($routes, KategoriController::class, PREFIX_TABLE.'keu_kategori');
        });

        //-------------------------------------Data Iuran Beasiswa -----------------------------------------------
        $routes->group('iuran', static function ($routes) {
            addDefaultRoutes($routes, IuranController::class, PREFIX_TABLE.'keu_iuran');
        });
    });
    
});
//-----------------------------------------------------------------------------------------------------