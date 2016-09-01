<?php
namespace SocialFoundation\Testing;


use CLMVC\Controllers\Render\RenderedContent;

/**
 * Class ControllerTestCaseHelper
 * @package helpers
 */
trait ControllerTestCaseHelper {
    private static $stream = '';

    /**
     * @return string
     */
    public static function getStream()
    {
        return self::$stream;
    }

    /**
     * @param $stream
     */
    public static function setStream($stream) {
        self::$stream = $stream;
    }

    /**
     * @param $expected
     * @param string $message
     */
    public function assertJsonCount($expected, $message = '') {
        $result = RenderedContent::get();
        $json = json_decode($result, true);
        $this->assertCount($expected, $json, $message);
    }

    /**
     * @param $expected
     * @param string $message
     */
    public function assertKeyInJson($expected, $message = '')
    {
        $result = RenderedContent::get();
        $json = json_decode($result, true);
        $this->assertArrayHasKey($expected, $json, $message);
    }

    /**
     * @param $expected
     * @param string $message
     */
    public function assertKeyNotInJson($expected, $message = '')
    {
        $result = RenderedContent::get();
        $json = json_decode($result, true);
        $this->assertArrayNotHasKey($expected, $json, $message);
    }

    /**
     * @param $expected
     * @param string $message
     */
    public function assertStatusCode($expected, $message = '')
    {
        global $clmvc_http_code;
        $this->assertEquals($expected, $clmvc_http_code, $message);
    }

    /**
     * @param $key
     * @param $expected
     * @param string $message
     */
    public function assertValueInJson($key, $expected, $message = '')
    {
        $result = RenderedContent::get();
        $json = json_decode($result, true);
        $this->assertEquals($expected, $json[$key], $message);
    }

    /**
     * @param $json_string
     * @param string $message
     */
    public function assertJsonString($json_string, $message='') {
        $this->assertJsonStringEqualsJsonString($json_string, $this->getJson(), $message);
    }

    /**
     * @param $expected
     * @param string $message
     */
    public function assertResponseEquals($expected, $message = '') {
        $result = RenderedContent::get();
        $this->assertEquals($expected, $result, $message);
    }

    /**
     * @return array|mixed
     */
    public function getJsonDecoded() {
        $data=RenderedContent::get();
        return json_decode($data, true);
    }

    public function getJson() {
        return RenderedContent::get();
    }
    /**
     * @param $data
     */
    public function setJsonData($data) {
        file_put_contents('php://input', $data);
    }

    public function setUp() {
        RenderedContent::clear();
        RenderedContent::endIt(false);
        require_once 'MockPHPStream.php';
        stream_wrapper_unregister("php");
        stream_wrapper_register("php", "\\helpers\\MockPhpStream");
    }

    public function tearDown() {
        RenderedContent::clear();
        RenderedContent::endIt(false);
        stream_wrapper_restore("php");
        $_GET=[];
        $_POST=[];
        unset($_SERVER['CONTENT_TYPE']);
    }
}
