<?php

require_once 'Config.php';

class Resource
{
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

        //string not found -> search in english dictionary
        $dictionary = self::getDictionary("en");
        foreach ($dictionary->string as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }

        //id not found
        return "";
    }

    private static function getUserLanguage()
    {
        if (strcmp(LANGUAGE_DETECTION, "header") === 0) {

            return self::getLanguageHeader();

        } else if (strcmp(LANGUAGE_DETECTION, "cookie") === 0) {

            //try to read cookies
            if (!isset($_COOKIE[COOKIE_NAME])) {
                $language = self::getLanguageHeader();
                setcookie(COOKIE_NAME, $language, time() + 60 * 60 * 24 * 2);
                return $language;
            } else {
                //cookie is already set
                return $_COOKIE[COOKIE_NAME];
            }

        } else if (strcmp(LANGUAGE_DETECTION, "rewrite") === 0) {

            $language = explode('/', trim($_SERVER["REQUEST_URI"], '/'))[0];
            //ISO 639-1 -> only 2 letters allowed
            return strlen($language) !== 2 ? self::getLanguageHeader() : $language;

        }

        //if nothing is set, return default language
        return DEFAULT_LANGUAGE;
    }

    private static function getLanguageHeader()
    {
        //set default language, if language could not be determined
        $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        return empty($language) ? DEFAULT_LANGUAGE : $language;
    }

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

    private static function dictionaryFileAvailable($file)
    {
        return (file_exists($file) && is_readable($file));
    }
}
