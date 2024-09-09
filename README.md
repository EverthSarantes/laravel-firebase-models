Here’s an updated version of the `README.md` with your requests:
# Laravel Firebase Models

`Laravel Firebase Models` is a package that allows you to seamlessly interact with Firebase Realtime Database in a Laravel project, using Eloquent-like models. This package abstracts the Firebase API into an intuitive interface for creating, reading, updating, and deleting records, along with relations similar to the ones in Laravel's ORM.

## Features
- Eloquent-style models for Firebase Realtime Database.
- CRUD operations (Create, Read, Update, Delete) via Firebase.
- Simple relationships like `belongsToOne` and `hasMany` for handling related data.
- Command to generate Firebase models quickly.

## Installation

### 1. Install via Composer

In your Laravel project directory, run the following command to install the package:

```bash
composer require everth/laravel-firebase-models --dev-main
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

### License

This package is open-sourced software licensed under the [MIT license](LICENSE).