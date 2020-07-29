<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OauthClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Permite criação de token via login / usuario e senha
        DB::table('oauth_clients')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'name' => 'GodotLaravel (Client)',
                'secret' => 'yTyqxWAmEHlnKOxtscXIXQAzAj5vxKKpHIBgOH4I',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
