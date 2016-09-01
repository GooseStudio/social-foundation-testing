<?php
namespace SocialFoundation\Testing;

/**
 * Class MockPhpStream
 * Source comes from http://news-from-the-basement.blogspot.se/2011/07/mocking-phpinput.html
 *
 */
class MockPhpStream{
    protected $index = 0;
    protected $length = null;

    public $context;

    /**
     * MockPhpStream constructor.
     */
    function __construct(){
        $this->index = 0;
        $this->length = strlen(APITestCase::getStream());
    }

    /**
     * @param $path
     * @param $mode
     * @param $options
     * @param $opened_path
     * @return bool
     */
    function stream_open($path, $mode, $options, &$opened_path){
        return true;
    }

    function stream_close(){
    }

    /**
     * @return array
     */
    function stream_stat(){
        return [];
    }

    /**
     * @return bool
     */
    function stream_flush(){
        return true;
    }

    /**
     * @param $count
     * @return string
     */
    function stream_read($count){
        $data=APITestCase::getStream();
        if(is_null($this->length) === TRUE){
            $this->length = strlen($data);
        }
        $length = min($count, $this->length - $this->index);
        $data = substr($data, $this->index);
        $this->index = $this->index + $length;
        return $data;
    }

    /**
     * @return bool
     */
    function stream_eof(){
        return ($this->index >= $this->length ? TRUE : FALSE);
    }

    /**
     * @param $data
     * @return int
     */
    function stream_write($data){
        APITestCase::setStream($data);
        return strlen($data);
    }

    /**
     *
     */
    function unlink(){
        $this->index = 0;
        $this->length = 0;
        APITestCase::setStream('');
    }
}