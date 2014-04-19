<?php

class DatabaseSeeder extends Seeder {

	protected static $number_articles_category = 30;

	protected static $categories = array(
		'Perforateur' => array('Description', 'Année'),
		'Corde' => array('Longueur', 'Année', 'Code', 'Corde_statique'),
		'Casque' => array('Année', 'Code'),
	);

	protected static $fields = array(
		"Description" => array(
			'type' => "text"),
		"Année" => array(
			'type' => "integerpositive"),
		"Corde_statique" => array(
			'type' => "boolean"),
		"Longueur" => array(
			'type' => "integerpositive"),
		"Code" => array(
			'type' => "integerpositive"),
	);

	public static function get_categories()
	{
		return self::$categories;
	}

	public static function get_fields()
	{
		return self::$fields;
	}

	public static function get_number_articles_category()
	{
		return self::$number_articles_category;
	}

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserTableSeeder');
		$this->call('FieldTableSeeder');
		$this->call('CategoryTableSeeder');
		$this->call('ArticleTableSeeder');
		$this->call('HistoryTableSeeder');
	}
}

class UserTableSeeder extends Seeder {

    public function run()
    {
		// admin user
        User::create(array(
			'email' => 'admin@local.mat',
			'given_name' => 'admin',
			'family_name' => 'admin',
			'password' => Hash::make('admin'),
			'enabled' => 1,
			'admin' => 1
		));

		// 50 random generated users
		$faker = Faker\Factory::create('en_GB');
		$count = 50;

		for ($i = 0; $i < $count; $i++)
		{
			User::create(array(
				'email' => $faker->unique()->email,
				'given_name' => $faker->firstName,
				'family_name' => $faker->lastName,
				'password' => Hash::make('123asd'),
				'enabled' => 1,
				'admin' => 0 ));
		}
	}
}

class FieldTableSeeder extends Seeder {

    public function run()
    {
		$field_data = DatabaseSeeder::get_fields();

		foreach($field_data as $field_name=>$data)
		{
			Field::create(array(
				'name' => $field_name,
				'type' => $data['type']));
		}
	}
}

class CategoryTableSeeder extends Seeder {

    public function run()
    {
		$category_data = DatabaseSeeder::get_categories();

		foreach($category_data as $category_name=>$field_names)
		{
			$category = new Category;
			$category->name = $category_name;
			$category->save();

			foreach($field_names as $field_name)
			{
				$field = Field::whereName($field_name)->first();
				$category->fields()->save($field);
			}
		}
	}
}

class ArticleTableSeeder extends Seeder {

    public function run()
    {
		$faker = Faker\Factory::create('en_GB');
		$count = DatabaseSeeder::get_number_articles_category();
		$category_data = DatabaseSeeder::get_categories();
		$field_data = DatabaseSeeder::get_fields();

		foreach($category_data as $category_name=>$field_names)
		{
			$category = Category::whereName($category_name)->first();

			for ($i = 0; $i < $count; $i++)
			{
				$article = new Article;
				$article->category()->associate($category);
				$article->save();

				foreach ($field_names as $field_name)
				{
					$attribute = new Attribute;
					$field = Field::whereName($field_name)->first();				
	
					switch($field_name)
					{
						case 'Description':
							$attribute->value = $faker->sentence(3);
							break;
						case 'Année':
							$attribute->value = $faker->randomNumber(1990,2013);
							break;
						case 'Corde_statique':
							$attribute->value = $faker->boolean(80);
							break;
						case 'Longueur':
							$attribute->value = round($faker->randomNumber(10,150), -1);
							break;
						case 'Code':
							$attribute->value = $faker->unique()->randomNumber(2);
							break;
					}

					$attribute->field()->associate($field);
					$attribute->article()->associate($article);	
					$attribute->save();
				}
			}
		}
	}
}

class HistoryTableSeeder extends Seeder {

    public function run()
    {
		$faker = Faker\Factory::create('en_GB');
		$users = User::all();
		$max_id = DatabaseSeeder::get_number_articles_category()
			* count( DatabaseSeeder::get_categories() );
		
		foreach ($users as $user)
		{
			// currently borrowed articles
			while ( $faker->boolean(50) )
			{
				$article_id = $faker->unique()->randomNumber(1, $max_id);
				$date_borrowed = $faker->dateTimeThisYear('now');

				$article = Article::find($article_id);
				$article->borrowed = true;
				$article->save();

				History::create(array(
					'user_id' => $user->id,
					'article_id' => $article_id,
					'created_at' => $date_borrowed->format('Y-m-d H:i:s'),
					'updated_at' => $date_borrowed->format('Y-m-d H:i:s') ));
			}

			// history for some returned articles
			while ( $faker->boolean(90) )
			{
				$article_id = $faker->randomNumber(1, $max_id);
				$date_borrowed = $faker->dateTimeThisDecade('now');
				$date_interval = DateInterval::createFromDateString(
					$faker->randomNumber(1,30).' day' );
				$date_returned = clone $date_borrowed;
				$date_returned->add($date_interval);

				$article = Article::find($article_id);
				$article->borrowed = false;
				$article->save();

				History::create(array(
					'user_id' => $user->id,
					'article_id' => $article_id,
					'created_at' => $date_borrowed->format('Y-m-d H:i:s'),
					'updated_at' => $date_borrowed->format('Y-m-d H:i:s'),
					'returned_at' => $date_returned->format('Y-m-d H:i:s') ));
			}	
		}
    }
}

