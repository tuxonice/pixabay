# pixabay

[![API Testing](https://img.shields.io/badge/API%20Test-RapidAPI-blue.svg)](https://rapidapi.com/package/Pixabay?utm_source=PixabayGithub&utm_medium=button&utm_content=Vender_GitHub)

A wrapper to Pixabay API (https://pixabay.com/api/docs/)

## USE

```
define('_DS','\\');
require('class.apiCall.php');
require('class.Database.php');
require('class.pixaBay.php');
include('dBug.php');

$apiKey = 'YOUR API KEY'; //See https://pixabay.com/api/docs/
$dbConfig = array();
$dbConfig['host'] = 'localhost';
$dbConfig['username'] = 'dbusername';
$dbConfig['password'] = 'dbpassword';
$dbConfig['dbname'] = 'dbname';

$pixaBay = new pixaBay($dbConfig, $apiKey);

$pixaBay->getImagesByCategory();
```
