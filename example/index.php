<?php
//use composer import here!
require_once '../src/Resource.php';
require_once '../src/Config.php';

//get default config or set it manually
$config = Config::getDefaults();
$resource = new Resource($config);
echo $resource->loadString("sample_text");
