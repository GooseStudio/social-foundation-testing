<?php

namespace SocialFoundation\Testing;

use ArtOfWP\WP\Testing\WP_UnitTestCase;
use CLMVC\Controllers\Render\RenderedContent;
use CLMVC\Core\Container;
use CLMVC\Core\Http\Routes;
use CLMVC\Events\Hook;
use CLMVC\ViewEngines\WordPress\WpRendering;
use helpers\MockPhpStream;

class API_WP_TestCase extends WP_UnitTestCase
{
    private static $stream = '';
    public function get($path, $get=[], $post = []) {
        $this->execute($path, $get, $post, 'get');
    }

    public function create($path, $post, $get=[]) {
        $this->execute($path, $get, $post, 'post');
    }

    public function update($path, $post, $get=[]) {
        $this->execute($path, $get, $post, 'put');
    }

    public function delete($path, $get=[]) {
        $this->execute($path, $get, [], 'delete');
    }

    public function execute($path, $get=[], $post=[], $method='get') {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $path;
        $_GET=$get;
        if($post) {
            $_SERVER['CONTENT_TYPE'] = 'application/json';
            self::setJsonData(json_encode($post));
        }
        do_action('init');
        $this->assertTrue(Routes::instance()->isRouted(), 'Route was not found: '.$path);
    }

    public static function getStream()
    {
        return self::$stream;
    }

    public static function setStream($stream) {
        self::$stream = $stream;
    }

    public static function assertJsonCount($expected, $message = '') {
        $json = self::getJsonDecoded();
        self::assertCount($expected, $json, $message);
    }

    public static function assertKeyInJson($expected, $message = '')
    {
        $json = self::getJsonDecoded();
        self::assertArrayHasKey($expected, $json, $message);
    }

    public static function assertKeyNotInJson($expected, $message = '')
    {
        $json = self::getJsonDecoded();
        self::assertArrayNotHasKey($expected, $json, $message);
    }

    public static function assertStatusCode($expected, $message = '')
    {
        global $clmvc_http_code;
        self::assertEquals($expected, $clmvc_http_code, $message);
    }

    public static function assertValueInJson($key, $expected, $message = '')
    {
        $json = self::getJsonDecoded();
        self::assertEquals($expected, $json[$key], $message);
    }
    public static function assertResponseEquals($expected, $message = '') {
        $result = RenderedContent::get();
        self::assertEquals($expected, $result, $message);
    }

    public static function getJsonDecoded() {
        $data=RenderedContent::get();
        return json_decode($data, true);
    }

    public static function setJsonData($data) {
        file_put_contents('php://input', $data);
    }

    public function setUp() {
        parent::setUp();
        $renderer = Container::instance()->fetchOrMake(WpRendering::class);
        remove_action('init', [$renderer, 'renderText'], 99999);
        RenderedContent::clear();
        RenderedContent::endIt(false);
        stream_wrapper_unregister("php");
        stream_wrapper_register("php", MockPhpStream::class);
    }

    public function tearDown() {
        RenderedContent::clear();
        RenderedContent::endIt(false);
        stream_wrapper_restore("php");
        $_GET=[];
        $_POST=[];
        unset($_SERVER['CONTENT_TYPE']);
        parent::tearDown();
    }
}
