# Build a React Single Page Application with Laravel Sanctum API

Sanctum is Laravel’s lightweight API authentication package. I am using Sanctum to authenticate a React-based single-page app (SPA) with a Laravel backend. Assuming the front- and back-end of the app are sub-domains of the same top-level domain, we can use Sanctum’s cookie-based authentication, thereby saving us the trouble of managing API tokens. To this end, I’ve set up Homestead to give me two domains: `api.sanctum.test`, which points to the `public` folder of `backend` (the new Laravel project which we’ll create), and `sanctum.test`, which points to a completely separate directory, `frontend`. I’ve also provisioned a MySQL database, `sanctum_backend`.

### The backend

##### DLet’s start with the API:

```
laravel new backend
```

Our API could be anything – let’s say it’s for a library, and we have just one resource, books. We can create most of what we need with one artisan command:

```
php artisan make:model Book -mr
```

The -m flag generates a migration, while -r creates a resourceful controller with methods for all the CRUD operations you will need. For this tutorial we will only need index, but it’s good to know this option exists. So, let’s create a couple of fields in the migration:

```
Schema::create('books', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('author');
    $table->timestamps();
});
```

##### Setup database in .env file

```
DB_DATABASE=dbname
DB_USERNAME=user
DB_PASSWORD=password
```

…and run the migration (don’t forget to update the .env file with your database credentials):

```
php artisan migrate
```

Now update `DatabaseSeeder.php` to give us some books (and a user for later):

```
<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        Book::truncate();
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 50; $i++) {
            Book::create([
                'title' => $faker->sentence,
                'author' => $faker->name,
            ]);
        }

        User::truncate();
        User::create([
            'name' => 'Alex',
            'email' => 'alex@alex.com',
            'password' => Hash::make('pwdpwd'),
        ]);

    }
}

```

Now run `php artisan db:seed` to seed this data. Finally, we need to create the route and the controller action. That’s simple enough. Add this to the `routes/api.php` file:

```
Route::middleware('auth:sanctum')->get('/book', [App\Http\Controllers\BookController::class, 'index'])->name('book');

```

and then in the index method of BookController, return all the books:

```
return response()->json(Book::all());
```

Now if we hit api.sanctum.test/api/book in our browser or HTTP client of choice (Postman, Insomnia, etc), you should see a list of all the books.

##### Install Laravel Sanctum.

```
composer require laravel/sanctum
```

##### Publish the Sanctum configuration and migration files.

```
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

```

##### Run your database migrations.

```
php artisan migrate

```

##### Add the Sanctum's middleware.

```
../app/Http/Kernel.php

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

...

    protected $middlewareGroups = [
        ...

        'api' => [
            EnsureFrontendRequestsAreStateful::class,
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    ...
],

```

##### To use tokens for users.

```
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
}

```

##### Let's create the seeder for the User model

```javascript
php artisan make:seeder UsersTableSeeder
```

##### Now let's insert as record

```javascript
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
...
...
DB::table('users')->insert([
    'name' => 'John Doe',
    'email' => 'john@doe.com',
    'password' => Hash::make('password')
]);
```

##### To seed users table with user

```javascript
php artisan db:seed --class=UsersTableSeeder
```

##### create a controller nad /login route in the routes/api.php file:

```javascript
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    //

    function index(Request $request)
    {
        $user= User::where('email', $request->email)->first();
        // print_r($data);
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response([
                    'message' => ['These credentials do not match our records.']
                ], 404);
            }

             $token = $user->createToken('my-app-token')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

             return response($response, 201);
    }
}


```

##### Test with postman, Result will be below

```javascript

{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@doe.com",
        "email_verified_at": null,
        "created_at": null,
        "updated_at": null
    },
    "token": "AbQzDgXa..."
}

```

##### Make Details API or any other with secure route

```javascript

Route::group(['middleware' => 'auth:sanctum'], function(){
    //All secure URL's

    });


Route::post("login",[UserController::class,'index']);

```
