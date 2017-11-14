<?php

/**
 * Class Config Wrapper
 */
class Config
{
    private $values = [];

    public static function getDefaults()
    {
        $config = new Config();

        $config->set('DOCUMENT_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
        $config->set('PATH_TO_DICTIONARIES', $config->get('DOCUMENT_ROOT') . 'dictionary' . DIRECTORY_SEPARATOR);
        $config->set('DEFAULT_LANGUAGE', 'en');
        $config->set('DEFAULT_DICTIONARY', 'default.xml');
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
        $config->set('LANGUAGE_DETECTION', 'cookie');
        $config->set('COOKIE_NAME', 'language');
        $config->set("SUPPORTED_LANGUAGES", array(
                $config->get('DEFAULT_LANGUAGE') => $config->get('DEFAULT_DICTIONARY'),
//                "de" => "german.xml",
//                "es" => "spanish.xml"
            )
        );
        return $config;
    }

    public function set($key, $value)
    {
        $this->values[$key] = $value;

    }

    public function get($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }

        return null;
    }
}
