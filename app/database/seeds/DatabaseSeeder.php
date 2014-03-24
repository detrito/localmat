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
		$this->call('HistoryTableSeeder');
		$this->call('FieldTableSeeder');
		$this->call('CategoryTableSeeder');
		$this->call('ArticleTableSeeder');
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

class HistoryTableSeeder extends Seeder {

    public function run()
    {
		$user_id = DB::table('lm_users')
			->select('id')
			->where('given_name', 'admin')
			->first()
			->id;
		
		$article_id = 1;

        History::create(array(
			'borrowed' => true,
			'user_id' => $user_id
		));
    }
}

class FieldTableSeeder extends Seeder {

    public function run()
    {
		//DB::table('lm_fields')->delete();
		
		Field::create(array(
			'name' => "Description",
			'type' => "text",
			'rule' => "required|alpha_spaces|max:64",
		));
		
		Field::create(array(
			'name' => "Corde statique",
			'type' => "boolean",
			'rule' => "integer|between:0,1",
		));

		Field::create(array(
			'name' => "Longueur",
			'type' => "integerpositive",
			'rule' => "required|integer|between:0,10000",
		));
	}
}

class CategoryTableSeeder extends Seeder {

    public function run()
    {
		$category = new Category;
		$category->name = "Perforateur";
		$category->save();
		
		$field = Field::whereName('Description')->first();
		$category->fields()->save($field);
		
		$category = new Category;
		$category->name = "Corde";
		$category->save();

		$field = Field::whereName('Longueur')->first();
		$category->fields()->save($field);
		$field = Field::whereName('Corde statique')->first();
		$category->fields()->save($field);
	}
}

class ArticleTableSeeder extends Seeder {

    public function run()
    {
		// add a Perseuse article
		$category = Category::whereName('Perforateur')->first();
	
		$article = new Article;
		$article->category()->associate($category);
		$article->save();
	
		// add one attribute to the Article perseuse
		$field = Field::whereName('Description')->first();	
		$attribute = new Attribute;
		$attribute->value = "Bosch PSR XXX";
		$attribute->field()->associate($field);
		$attribute->article()->associate($article);	
		$attribute->save();

		// add a Corde article
		$category = Category::whereName('Corde')->first();
		$history = History::all()->first();	

		$article = new Article;
		$article->category()->associate($category);
		// set this article as borrowed by user 'admin'
		$article->history()->associate($history);
		$article->save();

		/// add one attribute to the article Corde
		$field = Field::whereName('Corde statique')->first();	
		$attribute = new Attribute;
		$attribute->value = "1";
		$attribute->field()->associate($field);
		$attribute->article()->associate($article);	
		$attribute->save();

		/// add another attribute to the article Corde
		$field = Field::whereName('Longueur')->first();
		$attribute = new Attribute;
		$attribute->value = "30";
		$attribute->field()->associate($field);
		$attribute->article()->associate($article);	
		$attribute->save();

		// add a Corde article
		$category = Category::whereName('Corde')->first();
	
		$article = new Article;
		$article->category()->associate($category);
		$article->save();

		/// add one attribute to the article Corde
		$field = Field::whereName('Corde statique')->first();	
		$attribute = new Attribute;
		$attribute->value = "1";
		$attribute->field()->associate($field);
		$attribute->article()->associate($article);	
		$attribute->save();

		/// add another attribute to the article Corde
		$field = Field::whereName('Longueur')->first();
		$attribute = new Attribute;
		$attribute->value = "20";
		$attribute->field()->associate($field);
		$attribute->article()->associate($article);	
		$attribute->save();

		// add a Corde article
		$category = Category::whereName('Corde')->first();
	
		$article = new Article;
		$article->category()->associate($category);
		$article->save();

		/// add one attribute to the article Corde
		$field = Field::whereName('Corde statique')->first();	
		$attribute = new Attribute;
		$attribute->value = "1";
		$attribute->field()->associate($field);
		$attribute->article()->associate($article);	
		$attribute->save();

		/// add another attribute to the article Corde
		$field = Field::whereName('Longueur')->first();
		$attribute = new Attribute;
		$attribute->value = "60";
		$attribute->field()->associate($field);
		$attribute->article()->associate($article);	
		$attribute->save();
	}
}
