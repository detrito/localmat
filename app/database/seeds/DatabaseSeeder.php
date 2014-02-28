<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserTableSeeder');
	}

}

class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('lm_users')->delete();

        User::create(array(
			'email' => 'admin@local.mat',
			'given_name' => 'admin',
			'family_name' => 'admin',
			'password' => Hash::make('admin'),
			'enabled' => 1,
			'admin' => 1
		));
    }
}

