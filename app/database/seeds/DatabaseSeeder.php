<?php

class DatabaseSeeder extends Seeder {

	protected static $number_articles_category = 60;

	protected static $categories_amounts = array(
		'Mousquetons' => 25,
		'Combinaisons' => 5,
		'Plaquettes' => 30
	);

	protected static $categories_singles = array(
		'Perforateur' => array('Code', 'Description', 'Année', 'Remarque'),
		'Corde' => array('Code', 'Longueur', 'Année', 'Corde statique', 'Remarque'),
		'Casque' => array('Code', 'Année', 'Remarque'),
		'Boudrier' => array('Code', 'Année', 'Remarque'),
		'Poignée' => array('Code', 'Année', 'Remarque'),
		'Bloqueur ventral' => array('Code', 'Année', 'Remarque'),
		'Kit' => array('Code', 'Description', 'Remarque'),
		'Pharmacie' => array('Code', 'Description', 'Remarque')
	);

	protected static $fields = array(
		"Description" => array(
			'type' => "string",
			'required' => false,
			'main' => 0),
		"Année" => array(
			'type' => "integerpositive",
			'required' => false,
			'main' => 0),
		"Corde statique" => array(
			'type' => "boolean",
			'required' => false,
			'main' => 0),
		"Longueur" => array(
			'type' => "integerpositive",
			'required' => true,
			'main' => 0),
		"Code" => array(
			'type' => "integerpositive",
			'required' => true,
			'main' => 1),
		"Remarque" => array(
			'type' => "string",
			'required' => false,
			'main' => 0)
	);

	public static function get_categories_amounts()
	{
		return self::$categories_amounts;
	}

	public static function get_categories_singles()
	{
		return self::$categories_singles;
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
		$this->call('CategorySingleTableSeeder');
		$this->call('ArticleAmountTableSeeder');
		$this->call('ArticleSingleTableSeeder');
		$this->call('HistorySingleTableSeeder');		
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
				'type' => $data['type'],
				'required' => $data['required'],
				'main' => $data['main'],));
		}
	}
}

class CategorySingleTableSeeder extends Seeder {

    public function run()
    {
		$category_data = DatabaseSeeder::get_categories_singles();

		foreach($category_data as $category_name=>$field_names)
		{
			$category = new Category;
			$category->name = $category_name;
			$category->article_class = 'ArticleSingle';
			$category->save();

			foreach($field_names as $field_name)
			{
				$field = Field::whereName($field_name)->first();
				$category->fields()->save($field);
			}
		}
	}
}

class ArticleAmountTableSeeder extends Seeder {

	public function run()
	{
		$category_data = DatabaseSeeder::get_categories_amounts();

		foreach($category_data as $category_name=>$amount)
		{
			// Create Category
			$category = new Category;
			$category->name = $category_name;
			$category->article_class = 'ArticleAmount';
			$category->save();

			// Create Article 
			$article = new Article;
			$article->category()->associate($category);
			$article->save();

			// Create ArticleAmount and save $values
			$article_amount = new ArticleAmount;
			$article_amount->available_items = $amount;
			$article_amount->total_items = $amount;
			$article_amount->save();
			// Associate ArticleAmount to Article
			$article_amount->article()->save($article);				
		}

	}
}

class ArticleSingleTableSeeder extends Seeder {

    public function run()
    {
		$faker = Faker\Factory::create('en_GB');
		$count = DatabaseSeeder::get_number_articles_category();
		$category_data = DatabaseSeeder::get_categories_singles();
		$field_data = DatabaseSeeder::get_fields();

		foreach($category_data as $category_name=>$field_names)
		{
			$category = Category::whereName($category_name)->first();

			for ($i = 0; $i < $count; $i++)
			{
				$article_single = new ArticleSingle;
				$article_single->save();

				$article = new Article;
				$article->category()->associate($category);
				$article->save();
				// save polymorphic relation
				$article_single->article()->save($article);				

				foreach ($field_names as $field_name)
				{
					$field_datum = new FieldDatum;
					$field = Field::whereName($field_name)->first();				
	
					switch($field_name)
					{
						case 'Description':
							$field_datum->value = $faker->sentence(3);
							break;
						case 'Année':
							$field_datum->value = $faker->numberBetween(1990,2013);
							break;
						case 'Corde statique':
							$field_datum->value = $faker->boolean(80);
							break;
						case 'Longueur':
							$field_datum->value = round($faker->numberBetween(10,150), -1);
							break;
						case 'Code':
							$field_datum->value = $faker->unique()->randomNumber(3);
							break;
						case 'Remarque':
							if($faker->boolean(10))
								$field_datum->value = $faker->sentence(6);
					}

					$field_datum->field()->associate($field);
					$field_datum->articleSingle()->associate($article_single);	
					$field_datum->save();					
				}
			}
		}
	}
}

class HistorySingleTableSeeder extends Seeder {

    public function run()
    {
		$faker = Faker\Factory::create('en_GB');
		$users = User::all();
		$max_id = DatabaseSeeder::get_number_articles_category()
			* count( DatabaseSeeder::get_categories_singles() );
		
		foreach ($users as $user)
		{
			// history for some returned articles
			while ( $faker->boolean(90) )
			{
				$article_id = $faker->numberBetween(1, $max_id);
				$date_borrowed = $faker->dateTimeThisDecade('now');
				$date_interval = DateInterval::createFromDateString(
					$faker->numberBetween(1,30).' day' );
				$date_returned = clone $date_borrowed;
				$date_returned->add($date_interval);
				
				// Set random $amount_items for articles of class ArticleAmount
				$article = Article::find($article_id);
				if($article->proprieties_type == 'ArticleAmount')
				{
					$categories_data = DatabaseSeeder::get_categories_amounts();
					$max_amount = $categories_data[$article->category->name];
					$amount_items = $faker->numberBetween(1,$max_amount);
				}
				else
				{
					$amount_items = 0;
				}
				
				History::create(array(
					'user_id' => $user->id,
					'article_id' => $article_id,
					'amount_items' => $amount_items,
					'created_at' => $date_borrowed->format('Y-m-d H:i:s'),
					'updated_at' => $date_borrowed->format('Y-m-d H:i:s'),
					'returned_at' => $date_returned->format('Y-m-d H:i:s') ));
			}

		}
    }
}

