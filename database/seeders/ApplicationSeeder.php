<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = [
            [
                'app_name'     => 'Aplikasi SSO',
                'client_id'    => 'sso-lapi',
                'url_aplikasi' => 'localhost:8000',
            ],
            [
                'app_name'     => 'Aplikasi Pemanis',
                'client_id'    => 'pemanis-sso',
                // Berdasarkan skema DB Anda, kolom ini boleh NULL.
                // Lebih baik menggunakan null daripada string 'none' agar data lebih rapi.
                'url_aplikasi' => null, 
            ],
            [
                'app_name'     => 'Aplikasi Sista',
                'client_id'    => 'sista-client',
                'url_aplikasi' => 'localhost/sistadev',
            ],
            [
                'app_name'     => 'Aplikasi ITBLAB',
                'client_id'    => 'itblab-client',
                'url_aplikasi' => 'localhost:8080',
            ]
        ];

        DB::table('applications')->insert($applications);
    }
}