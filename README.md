# 🚀 PHP Simple Framework

A lightweight, high-performance MVC framework built for modern PHP applications.

## 🤔 Why Choose This Framework?

### Simplicity First
- **Minimal Learning Curve**: Start building immediately with intuitive concepts
- **Clean Architecture**: Straightforward MVC implementation
- **Developer Friendly**: Clear and predictable behavior
- **Readable Code**: No magic methods or complex abstractions

### Performance & Efficiency
- **Fast Request Processing**: Optimized routing and middleware chain
- **Low Memory Footprint**: Only loads required components
- **Efficient Caching**: Redis and filesystem cache support
- **Quick Response Time**: Direct response handling

### Built on Solid Foundations
- **Laravel Components**:
  - Eloquent ORM for database operations
  - HTTP handling
  - Query Builder
- **Symfony Components**:
  - Cache system
  - Session management
- **Other High-Quality Packages**:
  - BladeOne for template rendering
  - Monolog for logging
  - PHP Debug Bar for debugging

### Flexibility & Extensibility
- **Regex-Based Routing**: Powerful pattern matching
- **Chainable Middleware**: Easy request/response manipulation
- **Environment Adaptable**: Works in various hosting setups
- **Multiple Cache Drivers**: Redis or filesystem
- **Session Flexibility**: Redis or filesystem storage

## ✨ Features

- MVC Architecture
- Advanced Routing with Regex Support
- Middleware System
- Template Engine (BladeOne)
- Session Management
- Cache System
- Debug Bar Integration
- Queue Management
- Environment Configuration
- Exception Handling
- Logging System

## 📦 Installation

```bash
composer require framework/framework
```

## 🗺️ Routing

### Basic Routing
```php
// Basic Controller@action syntax
$App::get('/users', 'UserController@index');
$App::post('/users', 'UserController@store');

// Method chaining style
$App->get('/users/{id}')
    ->setAction('UserController@show')
    ->setMiddleware([\App\Middlewares\AuthMiddleware::class]);

// Catch-all routes
$App->all("/.*")
    ->setAction("CatchAll@query")
    ->setMiddleware([\App\Middlewares\RedirectionMiddleware::class]);
```

### Route Parameters
```php
// Named parameters
$App::get('/users/{id}', 'UserController@show');

// Multiple parameters
$App::get('/posts/{postId}/comments/{commentId}', 'CommentController@show');
```

## 🔒 Middleware

### Middleware Registration
```php
// Global middleware
$App->use("/.*", [
    \App\Middlewares\SessionMiddleware::class
]);

// Pattern-specific middleware
$App->use("^/admin/.*", [
    \App\Middlewares\AdminAuthMiddleware::class,
    \App\Middlewares\LoggingMiddleware::class
]);

// Conditional middleware
if (isset($_ENV['DEBUG'])) {
    $App->use("/.*", [\App\Middlewares\DebugBarMiddleware::class]);
}
```

### Creating Middleware
```php
namespace App\Middlewares;

use Framework\Middleware;

class AuthMiddleware extends Middleware
{
    public function handle($request, $response)
    {
        // Pre-controller logic
        if (!isAuthenticated()) {
            return redirect('/login');
        }
        
        // Continue to next middleware
        return parent::next($request, $response);
        
        // Post-controller logic can be added here
    }
}
```

## 📁 Project Structure

```
your-project/
├── App/
│   ├── Controllers/         # Request handlers
│   ├── Middlewares/        # Custom middleware
│   ├── Models/             # Data models
│   └── Views/              # Blade templates
│       ├── layouts/
│       └── components/
│
├── Config/
│   ├── .env               # Environment variables
│   └── middlewares.php    # Middleware config
│
├── public/
│   ├── index.php         # Entry point
│   ├── css/
│   └── js/
│
├── cache/
│   └── views/            # Compiled templates
│
├── storage/
│   ├── logs/            # Application logs
│   └── sessions/        # File sessions
│
└── vendor/
```

## 🔄 Request Lifecycle

1. **Request Initialization**
   - Request capture and normalization
   - Environment setup
   - Session initialization

2. **Middleware Chain**
   ```
   Request → M1 → M2 → M3 → Controller → Response
           ↑    ↓    ↑    ↓      ↑
           └────┴────┴────┘      ↓
         (Bidirectional flow)    Response
   ```

3. **Route Resolution**
   - Pattern matching
   - Parameter extraction
   - Controller identification

4. **Response Generation**
   - Controller execution
   - View rendering
   - Response formatting

## 🎮 Controllers

```php
namespace App\Controllers;

use Framework\Controller;

class UserController extends Controller
{
    public function index($params)
    {
        return $this->view('users.index', [
            'users' => User::all()
        ]);
    }
}
```

## 📝 Views

```php
<!-- views/users/index.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>Users</h1>
    @foreach($users as $user)
        <div>{{ $user->name }}</div>
    @endforeach
@endsection
```

## 💾 Caching

```php
// Redis or filesystem caching
$cache = CacheManager::instance();

// Store data
$cache->set('users', $users, 3600);

// Retrieve data
$users = $cache->get('users');
```

## 📊 Debug Bar

Built-in debugging features:
- Request/Response info
- Cache operations
- Server variables
- Execution time
- Redis status
- Query logging

## ⚡ Session Management

```php
$session = SessionHandler::getInstance();

// Store data
$session->set('user_id', 123);

// Retrieve data
$userId = $session->get('user_id');
```

## 🔄 Queue System

```php
// Add to queue
QueueManager::instance()->add('emails', [
    'to' => 'user@example.com',
    'subject' => 'Welcome'
]);

// Process queue
$job = QueueManager::instance()->remove('emails');
```

## 🛠️ Configuration (.env)

```env
APP_ENV=development
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_CACHE_PREFIX=cache_
REDIS_QUEUE_PREFIX=queue_
REDIS_SESSION_PREFIX=session_
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This framework is open-sourced software licensed under the MIT license.