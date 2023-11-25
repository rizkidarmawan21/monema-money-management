<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // create akun saldo
        $akun_saldo = \App\Models\AkunSaldo::create([
            'nama_akun' => 'Kas',
            'saldo' => 0,
            'keterangan' => 'Kas',
        ]);


        // create karyawan
        $karyawan = Karyawan::create([
            'nama' => 'Karyawan',
            'telepon' => '081234567890',
        ]);

        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class
        ]);
    }
}
