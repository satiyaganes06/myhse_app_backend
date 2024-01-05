<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$users = [

			//admin
			[
				'name' => 'satiya',
				'role' => 'admin',
				'email' => 'satiyaganes@gmail.com',
				'phone_number' => '01883728822',
				'password' => bcrypt('12345678'),
			],

			//user
			[
				'name' => 'ganes',
				'role' => 'user',
				'email' => 'satiyaganes.sg@gmail.com',
				'phone_number' => '01183728822',
				'password' => bcrypt('12345678'),
			],

		];
		

		foreach ($users as $key => $user) {
			User::create($user);
		}
	}
}
