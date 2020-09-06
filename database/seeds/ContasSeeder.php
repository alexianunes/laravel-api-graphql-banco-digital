<?php

use Illuminate\Database\Seeder;

class ContasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contas = [

        	[
        		'usuario_id' => '1',
                'agencia' => '0001',
                'conta' => '54321',
                'saldo' => 160
        	],

		];

        DB::table('contas')->insert($contas);
    }
}
