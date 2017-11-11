<?php

require_once 'Config.php';
require_once 'Config.php';

class Resource
{
    private $config;

    /**
     * Resource constructor.
     * @param $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }


    /**
     * loads the string from the user specified language set (xml file) for the requested id
     * @param $id
     * @return string on success,
     * empty string otherwise
     */
    public function loadString($id)
    {
        $language = self::getUserLanguage();
        $dictionary = self::getDictionary($language);

        //search for string in resources
        foreach ($dictionary->string as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }

        //string not found -> search in default dictionary
        $dictionary = self::getDictionary($this->config->get('DEFAULT_LANGUAGE'));
        foreach ($dictionary->string as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }

        //id not found
        return "";
    }

    /**
     * try to determine the user language with the specified method (Config.php);
     * default language will be returned on failure
     * @return string
     */
    private function getUserLanguage()
    {
        switch ($this->config->get('LANGUAGE_DETECTION')) {
            case "header":
                $detectedLanguage = self::getLanguageHeader();
                break;
            case "cookie":
                $detectedLanguage = self::getLanguageCookie();
                break;
            case "rewrite":
                $detectedLanguage = self::getLanguageRewrite();
                break;
            default:
                //if nothing is set, return default language
                $detectedLanguage = $this->config->get('DEFAULT_LANGUAGE');
        }
        return $detectedLanguage;
    }

    /**
     * Try to read $_SERVER['HTTP_ACCEPT_LANGUAGE'] and identify the language;
     * default language will be returned on failure
     * @return string
     */
    private function getLanguageHeader()
    {
        //set default language, if language could not be determined
        $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        return empty($language) ? $this->config->get('DEFAULT_LANGUAGE') : $language;
    }

    /**
     * Try to read cookie to identify the language;
     * If no cookie is set, it will use the 'header' method and try to set the cookie
     * @return string
     */
    private function getLanguageCookie()
    {
        //try to read cookies
        $cookieName = $this->config->get('COOKIE_NAME');
        if (!isset($_COOKIE[$cookieName])) {
            $language = self::getLanguageHeader();
            setcookie($cookieName, $language, time() + 60 * 60 * 24 * 2);
            return $language;
        } else {
            //cookie is already set
            return $_COOKIE[$cookieName];
        }
    }

    /**
     * splits the $_SERVER['REQUEST_URI'] and identify the language;
     * If no argument is set, it will use the 'header' method
     *
     * You can use a URL structure like example.com/en/index.php
     *
     * @return string
     */
    private function getLanguageRewrite()
    {
        $language = explode('/', trim($_SERVER["REQUEST_URI"], '/'))[0];
        //ISO 639-1 -> only 2 letters allowed
        return strlen($language) !== 2 ? self::getLanguageHeader() : $language;
    }

    /**
     * Creates a SimpleXMLElement of a XML file for the requested language;
     * empty SimpleXMLElement will be returned on failure
     * @param $language
     * @return SimpleXMLElement
     */
    private function getDictionary($language)
    {
        $arrSupportedLanguages = $this->config->get('SUPPORTED_LANGUAGES');

        //check if requested language is supported
        if (isset($arrSupportedLanguages[$language])) {
            $dictionaryName = $arrSupportedLanguages[$language];
        } else {
            $dictionaryName = $this->config->get('DEFAULT_DICTIONARY');
        }

        //set path to dictionary for user language
        $fileDictionary = $this->config->get('PATH_TO_DICTIONARIES') . $dictionaryName;

        //use default dictionary if file is not available
        if (!self::dictionaryFileAvailable($fileDictionary)) {
            $fileDictionary = $this->config->get('PATH_TO_DICTIONARIES') . $this->config->get('DEFAULT_DICTIONARY');
            if (!self::dictionaryFileAvailable($fileDictionary)) {
                //return empty xml, if default file is also not available
                return new \SimpleXMLElement("<resources></resources>");
            }
        }

        return simplexml_load_file($fileDictionary);
    }

    /**
     * Checks if the requested dictionary is available and readable
     * @param $file
     * @return bool
     */
    private function dictionaryFileAvailable($file)
    {
        return (file_exists($file) && is_readable($file));
    }
}
