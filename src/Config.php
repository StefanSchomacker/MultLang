<?php
define('DOCUMENT_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

//define your path to the dictionaries
define('PATH_TO_DICTIONARIES', DOCUMENT_ROOT . DIRECTORY_SEPARATOR . 'dictionary' . DIRECTORY_SEPARATOR);

//define your default language here
define('DEFAULT_LANGUAGE', 'en');

//define your default language set here
define('DEFAULT_DICTIONARY', 'default.xml');

/*
 * Choose between 'header' | 'cookie' | 'rewrite'
 *
 * header:
 *      this method uses $_SERVER['HTTP_ACCEPT_LANGUAGE']
 * cookie:
 *      this method uses cookies.
 *      If no cookie is set, it will use the 'header' method
 *      and try to set the cookie.
 * rewrite:
 *      this method splits the $_SERVER['REQUEST_URI']
 *      and identify the language
 *      If no argument is set, it will use the 'header' method
 *      You can use a URL structure like example.com/en/index.php
 */
define('LANGUAGE_DETECTION', 'cookie');
define('COOKIE_NAME', 'language');

//set all languages you want to support
define("SUPPORTED_LANGUAGES", serialize(
    array(
        DEFAULT_LANGUAGE => DEFAULT_DICTIONARY,
//        "de" => "german.xml",
//        "es" => "spanish.xml"
    )
));
