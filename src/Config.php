<?php

/**
 * Class Config Wrapper
 */
class Config
{
    const DOCUMENT_ROOT = 'DOCUMENT_ROOT';
    const PATH_TO_DICTIONARIES = 'PATH_TO_DICTIONARIES';
    const DEFAULT_LANGUAGE = 'DEFAULT_LANGUAGE';
    const DEFAULT_DICTIONARY = 'DEFAULT_DICTIONARY';
    const LANGUAGE_DETECTION = 'LANGUAGE_DETECTION';
    const COOKIE_NAME = 'COOKIE_NAME';
    const SUPPORTED_LANGUAGES = 'SUPPORTED_LANGUAGES';

    private $values = [];

    public static function getDefaults()
    {
        $config = new Config();

        $config->set(Config::DOCUMENT_ROOT, dirname(__DIR__) . DIRECTORY_SEPARATOR);
        $config->set(Config::PATH_TO_DICTIONARIES, $config->get(Config::DOCUMENT_ROOT) . 'dictionary' . DIRECTORY_SEPARATOR);
        $config->set(Config::DEFAULT_LANGUAGE, 'en');
        $config->set(Config::DEFAULT_DICTIONARY, 'default.xml');
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
        $config->set(Config::LANGUAGE_DETECTION, 'cookie');
        $config->set(Config::COOKIE_NAME, 'language');
        $config->set(Config::SUPPORTED_LANGUAGES, array(
                $config->get(Config::DEFAULT_LANGUAGE) => $config->get(Config::DEFAULT_DICTIONARY),
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
