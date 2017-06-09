<?php

require_once 'Config.php';

class Resource
{
    /**
     * loads the string from the user specified language set (xml file) for the requested id
     * @param $id
     * @return string on success,
     * empty string otherwise
     */
    public static function loadString($id)
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
        $dictionary = self::getDictionary(DEFAULT_LANGUAGE);
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
    private static function getUserLanguage()
    {
        if (strcmp(LANGUAGE_DETECTION, "header") === 0) {

            return self::getLanguageHeader();

        } else if (strcmp(LANGUAGE_DETECTION, "cookie") === 0) {

            return self::getLanguageCookie();

        } else if (strcmp(LANGUAGE_DETECTION, "rewrite") === 0) {

            return self::getLanguageRewrite();

        }

        //if nothing is set, return default language
        return DEFAULT_LANGUAGE;
    }

    /**
     * Try to read $_SERVER['HTTP_ACCEPT_LANGUAGE'] and identify the language;
     * default language will be returned on failure
     * @return string
     */
    private static function getLanguageHeader()
    {
        //set default language, if language could not be determined
        $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        return empty($language) ? DEFAULT_LANGUAGE : $language;
    }

    /**
     * Try to read cookie to identify the language;
     * If no cookie is set, it will use the 'header' method and try to set the cookie
     * @return string
     */
    private static function getLanguageCookie()
    {
        //try to read cookies
        if (!isset($_COOKIE[COOKIE_NAME])) {
            $language = self::getLanguageHeader();
            setcookie(COOKIE_NAME, $language, time() + 60 * 60 * 24 * 2);
            return $language;
        } else {
            //cookie is already set
            return $_COOKIE[COOKIE_NAME];
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
    private static function getLanguageRewrite()
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
    private static function getDictionary($language)
    {
        $arrSupportedLanguages = unserialize(SUPPORTED_LANGUAGES);

        //check if requested language is supported
        if (isset($arrSupportedLanguages[$language])) {
            $dictionaryName = $arrSupportedLanguages[$language];
        } else {
            $dictionaryName = DEFAULT_DICTIONARY;
        }

        //set path to dictionary for user language
        $fileDictionary = PATH_TO_DICTIONARIES . $dictionaryName;

        //use default dictionary if file is not available
        if (!self::dictionaryFileAvailable($fileDictionary)) {
            $fileDictionary = PATH_TO_DICTIONARIES . DEFAULT_DICTIONARY;
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
    private static function dictionaryFileAvailable($file)
    {
        return (file_exists($file) && is_readable($file));
    }
}
