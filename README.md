<img src="readMe-resources/phalcon-framework.png" width="400">

# aSimplePhalconProject

Simple Database management project using Phalcon.
A login functionality using Phalcon native CRSF tokens is implemented.

<img src="readMe-resources/crsf.png" width="400">

# Configuration 

## Overview of the data

<img src="readMe-resources/database.png" width="700">

## Database

To use the website, the database script located in **database-sample/blog.sql** must be run in the database.
The database name is by default "phalcon". It can be modified in the app/config/config.php file

```
return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => 'root',
        'dbname'      => 'phalcon',
        'charset'     => 'utf8',
    ],
...
])
```
