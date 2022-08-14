<?php
declare(strict_types=1);
#Notes:
#1. for IMDB, we use the `t=`, not `s=` (which is used for search)
#2. since APIs are supposed only to GET the data, we are only checking $_GET for respective values
#3. using JWT only for authorization, because I am afraid I'd get too deep into standardization of the format otherwise
#4. api key for omdapi (fake one obviously, not sharing my personal one) is hardcoded for the sake of the task.
#   In proper project it's better be set in some config file/class.
#5. The above is also true for JWT keys. In addition, in proper project we should parametrize them and setup in separate classes
#6. Depends on https://github.com/firebase/php-jwt (to be pe saved in "/php-jwt/src" folder at the same level as index.php)
#7. In proper project this should properly use Composer for dependencies. Not sure if it's worth to set up for a test task.

use Lamia\IMDB;
use Lamia\ISBN;

#Register autoloader
#In proper project I would imagine Composer's autoload being in use. For the task, though, appropriate namespaces are
#added manually at the end of the required file
require __DIR__. '/Lamia/Autoload.php';

#Send some basic headers
@header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
@header('Allow: GET, HEAD, OPTIONS');
@header('Content-Type: application/json; charset=utf-8');

#Get path by exploding the request URI. If this is a real website, I would direct all requests, which are not files to index.php through mod_rewrite rules
$uri = explode('/', trim(preg_replace('/(^[^?]*)((\?.*)|$)/u', '$1', $_SERVER['REQUEST_URI']), '/'));

if (!empty($uri[0])) {
    switch (strtolower($uri[0])) {
        case 'getmovie':
            echo (new IMDB())->getItem();
            break;
        case 'getbook':
            echo (new ISBN())->getItem();
            break;
        default:
            @header($_SERVER['SERVER_PROTOCOL'].' 400');
            echo '{"Failure": false, "Error": "Unsupported endpoint provided"}';
            break;
    }
} else {
    @header($_SERVER['SERVER_PROTOCOL'].' 400');
    echo '{"Failure": false, "Error": "No endpoint provided"}';
}
