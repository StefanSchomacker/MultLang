<?php
declare(strict_types=1);
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'autoload.php';

use PHPUnit\Framework\TestCase;

/**
 * @covers Resource
 */
class ResourceTest extends TestCase
{

    public function provider()
    {
        $configHeader = $this->getTestConfig();
        $configHeader->set("LANGUAGE_DETECTION", "header");
        $configRewrite = $this->getTestConfig();
        $configRewrite->set("LANGUAGE_DETECTION", "rewrite");

        return array(
            array(new Resource($configHeader)),
            array(new Resource($configRewrite)),
        );
    }

    private function getTestConfig()
    {
        $config = Config::getDefaults();
        $config->set("SUPPORTED_LANGUAGES", array(
                $config->get('DEFAULT_LANGUAGE') => $config->get('DEFAULT_DICTIONARY'),
                "de" => "german.xml",
            )
        );
        $config->set('DOCUMENT_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
        $config->set("PATH_TO_DICTIONARIES", $config->get('DOCUMENT_ROOT') . "resources" . DIRECTORY_SEPARATOR);
        return $config;
    }

    public function testConfig()
    {
        $config = $this->getTestConfig();

        $this->assertNotNull($config->get("DOCUMENT_ROOT"));
        $this->assertNotNull($config->get("PATH_TO_DICTIONARIES"));
        $this->assertNotNull($config->get("DEFAULT_LANGUAGE"));
        $this->assertNotNull($config->get("DEFAULT_DICTIONARY"));
        $this->assertNotNull($config->get("LANGUAGE_DETECTION"));
        $this->assertNotNull($config->get("COOKIE_NAME"));
        $this->assertNotNull($config->get("SUPPORTED_LANGUAGES"));
        $this->assertNull($config->get("not_valid"));
    }

    /**
     * @dataProvider provider
     */
    public function testValidReturn(Resource $resource)
    {
        /** @var Config $config */
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = "en";
        $_SERVER['REQUEST_URI'] = "example.com/en/index.php";
        $this->assertEquals("This is a sample text in english", $resource->loadString("sample_text"));
    }

    /**
     * @dataProvider provider
     */
    public function testFallbackLanguage(Resource $resource)
    {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = "es";
        $_SERVER['REQUEST_URI'] = "example.com/es/index.php";
        $this->assertEquals("This is a sample text in english", $resource->loadString("sample_text"));
    }

    /**
     * @dataProvider provider
     */
    public function testNotDefaultLanguage(Resource $resource)
    {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = "de";
        $_SERVER['REQUEST_URI'] = "example.com/de/index.php";
        $this->assertEquals("Das ist ein deutscher Beispieltext", $resource->loadString("sample_text"));
    }

    /**
     * @dataProvider provider
     */
    public function testDetectionFailed(Resource $resource)
    {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = "";
        $_SERVER['REQUEST_URI'] = "example.com/index.php";
        $this->assertEquals("This is a sample text in english", $resource->loadString("sample_text"));
        /** @var Config $config */
        $config = $resource->getConfig();
        $config->set('DEFAULT_LANGUAGE', 'de');
        $this->assertEquals("Das ist ein deutscher Beispieltext", $resource->loadString("sample_text"));
    }

    /**
     * @dataProvider provider
     */
    public function testFallbackXML(Resource $resource)
    {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = "de";
        $_SERVER['REQUEST_URI'] = "example.com/de/index.php";
        $this->assertEquals("String not found in german.xml", $resource->loadString("not_found_in_german"));
    }

    /**
     * @dataProvider provider
     */
    public function testNotFound(Resource $resource)
    {
        $this->assertEquals("", $resource->loadString("id_not_available"));
    }

}
