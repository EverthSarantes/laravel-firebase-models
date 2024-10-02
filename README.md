# Laravel Firebase Models

`Laravel Firebase Models` is a package that allows you to seamlessly interact with Firebase Realtime Database in a Laravel project, using Eloquent-like models. This package abstracts the Firebase API into an intuitive interface for creating, reading, updating, and deleting records, along with relations similar to the ones in Laravel's ORM.

## Features
- Eloquent-style models for Firebase Realtime Database.
- CRUD operations (Create, Read, Update, Delete) via Firebase.
- Simple relationships like `belongsToOne` and `hasMany` for handling related data.
- Command to generate Firebase models quickly.
- Firebase authentication with session management.

## Installation

### 1. Install via Composer

In your Laravel project directory, run the following command to install the package:

```bash
composer require everth/laravel-firebase-models dev-main
```

### 2. Set Firebase Credentials

Update your `.env` file with your Firebase credentials:

```env
FIREBASE_CREDENTIALS=/path/to/your/firebase_credentials.json
FIREBASE_DATABASE_URL=https://your-database-url.firebaseio.com
```

Ensure that the path to your Firebase credentials JSON file is correct, and replace the `FIREBASE_DATABASE_URL` with your Firebase project's Realtime Database URL.

## Usage

### Generating a Firebase Model

To generate a new Firebase model, use the provided Artisan command:

```bash
php artisan make:firebaseModel ModelName
```

This will create a new model in the `app/Models/Firebase` directory. You can specify the Firebase collection by setting the `$collection` property in the model.

Example:

```php
<?php

namespace App\Models\Firebase;

use Firebase\Models\FirebaseModel;

class User extends FirebaseModel
{
    protected $collection = 'users'; // Name of the Firebase collection
}
```

### CRUD Operations

Below are examples of how to use the model to perform CRUD operations.

#### 1. **Retrieving All Records**

```php
use App\Models\Firebase\User;

// Retrieve all users
$users = User::all();
```

#### 2. **Finding a Record by ID**

```php
use App\Models\Firebase\User;

// Find a user by their Firebase ID
$user = User::find('firebase_user_id');
```

#### 3. **Finding Records with a Query**

```php
use App\Models\Firebase\User;

// Find all users where 'age' is 25
$users = User::where('age', 25);
```

#### 4. **Creating a Record**

```php
use App\Models\Firebase\User;

// Create a new user
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 25,
]);
```

#### 5. **Updating a Record**

```php
use App\Models\Firebase\User;

// Update a user by their Firebase ID
$user = User::update('firebase_user_id', [
    'name' => 'John Doe Updated',
    'age' => 26,
]);
```

#### 6. **Saving a Model Instance**

```php
use App\Models\Firebase\User;

// Create a new user instance and save
$user = new User();
$user->name = 'John Doe';
$user->email = 'john@example.com';
$user->age = 25;
$user->save();

// To update:
$user->age = 26;
$user->save();
```

#### 7. **Deleting a Record**

```php
use App\Models\Firebase\User;

// Delete a user by their Firebase ID
User::destroy('firebase_user_id');

// Or delete through an instance
$user = User::find('firebase_user_id');
$user->delete();
```

### Relationships

#### 1. **belongsToOne**

The `belongsToOne` relationship is used to retrieve a parent model by referencing a foreign key in the current model.

For example, if an `Order` belongs to a `User`, define the relationship in the `Order` model like this:

```php
<?php

namespace App\Models\Firebase;

use Firebase\Models\FirebaseModel;

class Order extends FirebaseModel
{
    protected $collection = 'orders';

    public function user()
    {
        return $this->belongsToOne(User::class, 'user_id');
    }
}
```

Now, you can fetch the `user` associated with an order:

```php
$order = Order::find('firebase_order_id');
$user = $order->user();
```

**Firebase Database Structure:**

```json
{
  "orders": {
    "firebase_order_id": {
      "user_id": "firebase_user_id",
      "item": "Product Name",
      "quantity": 2
    }
  },
  "users": {
    "firebase_user_id": {
      "name": "John Doe",
      "email": "john@example.com"
    }
  }
}
```

#### 2. **hasMany**

The `hasMany` relationship is used to retrieve multiple child records associated with a parent model. For example, if a `User` has many `Order`s, define the relationship in the `User` model like this:

```php
<?php

namespace App\Models\Firebase;

use Firebase\Models\FirebaseModel;

class User extends FirebaseModel
{
    protected $collection = 'users';

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
```

Now, you can fetch all `orders` associated with a user:

```php
$user = User::find('firebase_user_id');
$orders = $user->orders();
```

**Firebase Database Structure:**

```json
{
  "users": {
    "firebase_user_id": {
      "name": "John Doe",
      "email": "john@example.com"
    }
  },
  "orders": {
    "firebase_order_id_1": {
      "user_id": "firebase_user_id",
      "item": "Product 1",
      "quantity": 2
    },
    "firebase_order_id_2": {
      "user_id": "firebase_user_id",
      "item": "Product 2",
      "quantity": 1
    }
  }
}
```

### Authentication

To enable Firebase authentication, you need to set the AUTH_GUARD in your .env file:

```.env
AUTH_GUARD=firebase
```

Next, define your guard in config/auth.php.

#### Define the Auth Guard
In your config/auth.php, add the following to define your firebase guard:

```php
'guards' => [
    'firebase' => [
        'driver' => 'firebase',
        'provider' => 'firebase_users',
    ],
    'api' => [
        'driver' => 'sanctum',
        'provider' => 'firebase_users',
    ],
],
    
'providers' => [
    'firebase_users' => [
        'driver' => 'firebase',
        'model' => Firebase\Auth\Models\User::class,
    ],
],
```

### User Model

You can use the User model like any other model, import the user model using: `Use Firebase\Auth\Models\User`

### Authentication Routes

#### Login Route

```php
Route::get('login/{username}/{password}', function ($username, $password, Request $request) {
    if (Auth::guard('firebase')->attempt(['username' => $username, 'password' => $password])) {
        $request->session()->regenerate(); // Regenerate the session
        return 'True';
    }
    return 'False';
})->name('login');
```

#### Logout Route
To log users out, you can define a logout route:

```php
Route::get('logout', function (Request $request) {
    Auth::guard('firebase')->logout();
    $request->session()->invalidate(); // Invalidate the session
    $request->session()->regenerateToken(); // Regenerate the token
    return 'Logout';
})->name('logout');
```

#### Check Authentication
To check if a user is authenticated:

```php
Route::get('check', function (Request $request) {
    return Auth::guard('firebase')->user();
})->middleware('auth:firebase');
```

### Api Authentication

For the api authentication is necesary install `Laravel Sanctum`

#### Middleware

Import the AuthenticateWithFirebaseTokens middleware in `bootstrap/app.php`

```php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Firebase\Auth\Middlewares\AuthenticateWithFirebaseTokens;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.firebase' => AuthenticateWithFirebaseTokens::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

#### Login Route

```php
Route::get('login/{usernamen}/{password}', function ($username, $password, Request $request) {
    if(Auth::guard('firebase')->attempt(['username' => $username, 'password' => $password])){
        $user = Auth::guard('firebase')->user();
        $token = $user->createToken('api-token');

        return response()->json([
            'message' => 'Authenticated',
            'token' => $token
        ]);
    }

    return response()->json([
        'message' => 'Unauthorized',
        'token' => null
    ], 401);
});
```

#### Logout Route

```php
Route::get('logout', function (Request $request) {
    Auth::user()->revokeAllTokens();

    return response()->json([
        'message' => 'Logged out'
    ]);
})->middleware('auth.firebase');
```

#### Check Authentication

```php
Route::get('/user', function (Request $request) {
    return Auth::user()->toArray();
})->middleware('auth.firebase');
```

### License

This package is open-sourced software licensed under the [MIT license](LICENSE).
