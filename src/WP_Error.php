<?php
namespace SocialFoundation\Testing;

/**
 * Class WP_Error
 * @package helpers
 */
class WP_Error
{
    /**
     * @var string
     */
    private $code;
    /**
     * @var string
     */
    private $message;

    /**
     * WP_Error constructor.
     * @param string $code
     * @param string $message
     */
    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function get_error_code()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function get_error_message()
    {
        return $this->message;
    }
}
