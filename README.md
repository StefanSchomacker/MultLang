# MultLang - PHP Multi-Language Support [![Build Status](https://travis-ci.org/StefanSchomacker/MultLang.svg?branch=master)](https://travis-ci.org/StefanSchomacker/MultLang)

## Overview
* All strings are saved in XML files
* Automatic language detection: Methods:
  * [header](#header)
  * [cookie](#cookie)
  * [rewrite](#rewrite)
* Simple string access

## Installation
It's very simple to include this library in your project.

**Composer**

add this to your `composer.json` and run `composer install`:
```
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/StefanSchomacker/MultLang"
        }
    ],
    "require": {
        "multlang/multlang": "dev-master"
    }
```

_or_

**Download Zip**

[https://github.com/StefanSchomacker/MultLang/archive/master.zip](https://github.com/StefanSchomacker/MultLang/archive/master.zip)

_or_

**Clone Git**

```
git clone https://github.com/StefanSchomacker/MultLang.git
```

## Getting Started

### Usage

Just create a config object once and call the load function in your PHP Script.

You can also view the example [here](https://github.com/StefanSchomacker/MultLang/tree/master/example).

_**index.php:**_
```php
<?php
//get default config or set it manually
$config = Config::getDefaults();
$resource = new Resource($config);
echo $resource->loadString("sample_text");
?>
```

_**/dictionary/default.xml:**_
```xml
<?xml version="1.0" encoding="utf-8" ?>
<resources>

    <!-- Header -->
    <string id="sample_text">This is a sample text in english</string>

</resources>
```

_**/dictionary/german.xml:**_
```xml
<?xml version="1.0" encoding="utf-8" ?>
<resources>

    <!-- Header -->
    <string id="sample_text">Das ist ein deutscher Beispieltext</string>

</resources>
```

### Configuration
Edit config values if needed:

const | default value | description
------------ | ------------- | -------------
DOCUMENT_ROOT | `dirname(__DIR__) . DIRECTORY_SEPARATOR` | Path to document root
PATH_TO_DICTIONARIES | `DOCUMENT_ROOT . DIRECTORY_SEPARATOR . 'dictionary' . DIRECTORY_SEPARATOR` | Path to dictionary folder. Folder contains all XML files
DEFAULT_LANGUAGE | `en` | Default language in ISO 639-1 format. The constant will be used, if language cannot be determined
DEFAULT_DICTIONARY | `default.xml` | This XML file will be used, if other files are not available. This file should contain all string items.
LANGUAGE_DETECTION | `cookie` | This defines the method to detect the language. Choose between **['header'](#header)**, **['cookie'](#cookie)** and **['rewrite'](#rewrite)**.
COOKIE_NAME | `language` | Default key for the cookie.
SUPPORTED_LANGUAGES | `array(DEFAULT_LANGUAGE => DEFAULT_DICTIONARY)` | Contains all supported languages. <br/> Example: `"de" => "german.xml",`

Example:
```php
<?php
$config = Config::getDefaults();
$config->set(Config::DOCUMENT_ROOT, "/");
?>
```

## Language detection methods

### header
This will try to read `$_SERVER['HTTP_ACCEPT_LANGUAGE']`. 
If this fails, `DEFAULT_LANGUAGE` will be returned.

### cookie
This will try to read a cookie with the key `COOKIE_NAME`. 
If no cookie is set, it will use the [header](#header) method and set the new cookie.

### rewrite
This will try to read `$_SERVER['REQUEST_URI']` and split it to identify the language.
If no argument is set, it will use the [header](#header) method
<br />
You can use a URL structure like **example.com/`en`/index.php**

## Details
* The used language format is ISO 639-1. For more information see [https://en.wikipedia.org/wiki/ISO_639-1](https://en.wikipedia.org/wiki/ISO_639-1)
* If the requested string is not available in the XML file, the script will try to search in the `DEFAULT_DICTIONARY` (default.xml) file. An empty string will be returned if nothing matches.

## Improvements
Feel free to create a new
[Issue](https://github.com/StefanSchomacker/MultLang/issues) or a 
[Pull request](https://github.com/StefanSchomacker/MultLang/pulls)

## License
    Copyright 2019 Stefan Schomacker

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
