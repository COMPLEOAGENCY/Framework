# Framework

PHP Framework MVC routing request

## Technologies

- PHP ("^7.1 || ^8.0")

# Documentation

## Start

```
use Georges\Framework\Framework;

require __DIR__ . '/vendor/autoload.php';

$App = new Framework();
$App::get('/', 'Home', 'home');
$App->run();
```

## Routing

You will **define all of the routes** for your application in the **Config/route.json file**.

```
{
	"path" : "/uri",
	"controller" : "ExampleController",
	"action" : "exampleAction",
	"method" : "GET",
	"param" : []
}
```

Or generate **routes dynamically**. The most basic routes simply accept a **URI**, **Controller**, **Method**

```
use Georges\Framework\Framework;

require __DIR__ . '/vendor/autoload.php';

$App = new Framework();
$App::get('/', 'ExampleController', 'exampleAction');
$App->run();
```

**Available Router Methods**

```
$App::get($uri, $controller, $action);
$App::post($uri, $controller, $action);
$App::put($uri, $controller, $action);
$App::delete($uri, $controller, $action);
$App::all($uri, $controller, $action);
```

## Controller

Your Controller must extends **Framework\Controller**

```
namespace  Georges\Controllers;
use Georges\Framework\Controller;

class ExampleController extends Controller
{
    public function exampleAction($params)
    {
		...
    }
}
```

In your controller, you can define **params** and and access them in your views

```
namespace  Georges\Controllers;
use Georges\Framework\Controller;

class ExampleController extends Controller
{
    public function exampleAction($params)
    {
        $params['example'] = "This is example";
		//Or like this
		$example = "This is example";
		...
    }
}
```

## Middleware

In **Config/middlewares.php file** define your Middleware.

```
return array(
    [
        'path'=>"/",
        'middleware'=>[\Georges\Middlewares\ExampleMiddleWare1::class,\Georges\Middlewares\ExampleMiddleWare2:class]
    ],
    [
        'path'=>"/example",
        'middleware'=>\Georges\Middlewares\ExampleMiddleWare::class
    ]
);
```

All your Middleware must contains **handle function** for call all next middleware

```
namespace Georges\Middlewares;

class ExampleMiddleWare extends \Georges\Framework\Middleware{
    function handle($httpRequest){
        echo  "ExampleMiddleWare run";
        return parent::next($httpRequest);
    }
}
```

## View

**Create View in Views directory**

```
Views
 ┣ 📂example
 ┃	┣ 📜exampleIndex.php
```

**Rendering View in Controller**

```
namespace  Georges\Controllers;
use Georges\Framework\Controller;

class ExampleController extends Controller
{
    public function exampleAction($params)
    {
		$params['example'] = "This is example"
		//Set directory where is your view
        self::setDirName("Example");
        return self::view('exampleIndex', $params);

		//You can also make
		//return self::view('Example/exampleIndex', $params);
    }
}

```

**Use params in your view**

```
<h1>Example</h1>

<p>{{example}}</p>

<p><?= $example ?></p>
```

**Add Custom CSS File in view**

Create directory in **Views/**

```
📦Views
 ┣ 📂css
 ┃ ┣ 📂example
 ┃ ┃ ┣ 📜style.css
```

In your **Controller and in your Action** add

```
$view = new View();
$view::addCss("example/style.css");
```

**Add Custom JS File in view**

Create directory in **Views/**

```
📦Views
 ┣ 📂js
 ┃ ┣ 📂example
 ┃ ┃ ┣ 📜main.js
```

In your **Controller and in your Action** add

```
$view = new View();
$view::addJs("example/main.js");
```

## Basic Auth Example

**Routing**

```
use Georges\Framework\Framework;

require __DIR__ . '/vendor/autoload.php';

session_start();
$App = new Framework();

$App::get('/exampleHome', 'exampleHome', 'home');
$App::get('/exampleLogin', 'exampleLogin', 'login');

$App->run();
```

**Config/middlewares.php**

```
return
    array(
        [
            'path' => "/exampleHome",
            'middleware' => \Georges\Middlewares\ExampleAuthMiddleware::class
        ],
        [
            'path' => "/exampleLogin",
            'middleware' => \Georges\Middlewares\ExampleSetAuthMiddleware::class
        ]
    );
```

**ExampleAuthMiddleware**

```
namespace Georges\Middlewares;

class ExampleAuthMiddleware extends \Georges\Framework\Middleware
{
    function handle($httpRequest)
    {
        if (isset($_SESSION['auth']) && $_SESSION['auth'] == true) {
        } else {
            redirect("/exampleLogin");
        }
        return parent::next($httpRequest);
    }
}
```

**ExampleSetAuthMiddleware**

```
namespace Georges\Middlewares;

class ExampleSetAuthMiddleware extends \Georges\Framework\Middleware
{
    function handle($httpRequest)
    {
        if ($httpRequest->getMethod() == 'POST') {
            if ($_POST['user'] == "admin" && $_POST['password'] == "1234") {
                $_SESSION['auth'] = true;
                redirect("/exampleHome");
            } else {
                flash('errorLogin', 'Wrong username or password... !', 'danger');
            }
        }

        if (isset($_SESSION['auth']) && $_SESSION['auth'] = true) {
            redirect("/exampleHome");
        }
        return parent::next($httpRequest);
    }
}
```

**ExampleHomeController && ExampleLoginController**

```
namespace  Georges\Controllers;
use Georges\Framework\Controller;

class ExampleLogin extends Controller
{
    public function login($params)
    {
        self::setDirName("login");
        return self::view('exampleLogin', $params);
    }
}
```

```
namespace  Georges\Controllers;
use Georges\Framework\Controller;

class Home extends Controller
{
    public function home($params)
    {
        $params['name'] = 'Marc';
        $params['age'] = '12 ans';
        $params['civ'] = 'M';
        self::setDirName("Home");
        self::view('exampleHome', $params);
    }
}
```

**Views**

```
//Home View
<h1>Hello {{civ}}.{{name}}</h1>
<p>You have {{age}} years old.</p>
```

```
//Login View
<h1>You are not connected</h1>

<form action="#" method="POST">
    <input name="user" type="text" placeholder="User">
    <input name="password" type="password" placeholder="Password">
    <button>Submit</button>
</form>
```
