# Cleup - Configuration

#### Installation

Install the `cleup/config` library using composer:

```
composer require cleup/config
```

##### Initialization
```php 
$configuration = new Cleup\Core\Configuration\Loader([
    'debug' => false,
    'cache' => true,
    'cachePath' => __DIR__ . '/cache/config',
    'configPath' => __DIR__ . '/config',
    'env' => true,
    'envPath' => __DIR__
]);

$configuration->load();
```

##### Creating configuration and environment files
config/app.php
```php

return [
    "site_name" =>  'Cleup',
    "is_active" => true,
    "port"   => 80
];
```
config/modules/articles.php
```php

return [
    "name" =>  'Articles',
    "description" => "This is the articles module"
];
```

.env (.env.production or .env.local if debugging is enabled)
```properties
APP_NAME=Cleup
BOOL_TRUE=true
BOOL_FALSE=false
# Comment
INTIGER=2007
FLOAT=22.122002
```

##### Methods

```php
use Cleup\Core\Configuration\Config;
use Cleup\Core\Configuration\Environment\Env;

# Get
// where “app” is the name of the file
Config::get('app.site_name'); // string(Cleup)
// where “modules” is the directory name and “articles” is the file name
Config::get('modules.articles.description'); // string(This is the articles module)
// The default value
Config::get('app.oops', 'Some kind of a default value'); // string(Some kind of a default value)
// Environment
Env::get('APP_NAME');  // string(Cleup);
Env::get('BOOL_TRUE'); // bool(true);
Env::get('INTIGER');   // int(2007);
Env::get('FLOAT');     // float(22.122002);
Env::get('OOPS', 404); // int(404);

# Set - The methods do not change the structure of the underlying configuration and environment files.
Config::set('modules.articles.id', 35);
Env::set('LEVEL', 'Admin');

# Helpers
config('app.site_name') // string(Cleup),
env('APP_NAME')         // string(Cleup),
```



