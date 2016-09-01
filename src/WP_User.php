<?php
namespace SocialFoundation\Testing;

/**
 * Class WP_User
 * @package helpers
 */
/**
 * User API: WP_User class
 *
 * Core class used to implement the WP_User object.
 *
 * @since 2.0.0
 *
 * @property string $nickname
 * @property string $description
 * @property string $user_description
 * @property string $first_name
 * @property string $user_firstname
 * @property string $last_name
 * @property string $user_lastname
 * @property string $user_login
 * @property string $user_pass
 * @property string $user_nicename
 * @property string $user_email
 * @property string $user_url
 * @property string $user_registered
 * @property string $user_activation_key
 * @property string $user_status
 * @property string $display_name
 * @property string $spam
 * @property string $deleted
 */
class WP_User
{
    /**
     * User data container.
     *
     * @since 2.0.0
     * @var object
     */
    public $data;
    /**
     * The user's ID.
     *
     * @since 2.1.0
     * @access public
     * @var int
     */
    public $ID = 0;
    /**
     * The individual capabilities the user has been given.
     *
     * @since 2.0.0
     * @access public
     * @var array
     */
    public $caps = [];
    /**
     * User metadata option name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    public $cap_key;
    /**
     * The roles the user is part of.
     *
     * @since 2.0.0
     * @access public
     * @var array
     */
    public $roles = [];
    /**
     * All capabilities the user has, including individual and role based.
     *
     * @since 2.0.0
     * @access public
     * @var array
     */
    public $allcaps = [];
    /**
     * The filter context applied to user data fields.
     *
     * @since 2.9.0
     * @access private
     * @var string
     */
    private $filter = null;
    /**
     * @var string
     */
    private $name;
    /**
     * @var int|string
     */
    private $blog_id;

    /**
     * Constructor.
     *
     * Retrieves the userdata and passes it to WP_User::init().
     *
     * @since 2.0.0
     * @access public
     *
     * @global \wpdb $wpdb WordPress database abstraction object.
     *
     * @param int|string|\stdClass|WP_User $id User's ID, a WP_User object, or a user object from the DB.
     * @param string $name Optional. User's username
     * @param int $blog_id Optional Site ID, defaults to current site.
     */
    public function __construct($id = 0, $name = '', $blog_id = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->blog_id = $blog_id;
    }

    /**
     * Magic method for unsetting a certain custom field.
     *
     * @since 4.4.0
     * @access public
     *
     * @param string $key User meta key to unset.
     */
    public function __unset($key)
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }
    }

    /**
     * Determine whether the user exists in the database.
     *
     * @since 3.4.0
     * @access public
     *
     * @return bool True if user exists in the database, false if not.
     */
    public function exists()
    {
        return !empty($this->ID);
    }

    /**
     * Retrieve the value of a property or meta key.
     *
     * Retrieves from the users and usermeta table.
     *
     * @since 3.3.0
     *
     * @param string $key Property
     * @return mixed
     */
    public function get($key)
    {
        return $this->__get($key);
    }

    /**
     * Magic method for accessing custom fields.
     *
     * @since 3.3.0
     * @access public
     *
     * @param string $key User meta key to retrieve.
     * @return mixed Value of the given user meta key (if set). If `$key` is 'id', the user ID.
     */
    public function __get($key)
    {
        if (isset($this->data[$key])) {
            $value = $this->data[$key];
        } else {
            $value = get_user_meta($this->ID, $key, true);
        }

        if ($this->filter) {
            $value = sanitize_user_field($key, $value, $this->ID, $this->filter);
        }

        return $value;
    }

    /**
     * Magic method for setting custom user fields.
     *
     * This method does not update custom fields in the database. It only stores
     * the value on the WP_User instance.
     *
     * @since 3.3.0
     * @access public
     *
     * @param string $key User meta key.
     * @param mixed $value User meta value.
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Determine whether a property or meta key is set
     *
     * Consults the users and usermeta tables.
     *
     * @since 3.3.0
     *
     * @param string $key Property
     * @return bool
     */
    public function has_prop($key)
    {
        return $this->__isset($key);
    }

    /**
     * Magic method for checking the existence of a certain custom field.
     *
     * @since 3.3.0
     * @access public
     *
     * @param string $key User meta key to check if set.
     * @return bool Whether the given user meta key is set.
     */
    public function __isset($key)
    {
        if (isset($this->data[$key])) {
            return true;
        }
    }

    /**
     * Return an array representation.
     *
     * @since 3.5.0
     *
     * @return array Array representation.
     */
    public function to_array()
    {
        return $this->data;
    }

    /**
     * Creates a dummy user
     * @param array<string|mixed> $properties
     * @return WP_User
     */
    public static function make($properties = [])
    {
        $default = [
            'ID' => 1, 'nickname' => 'donald','description' => 'cartoon character','first_name' => 'Donald',
            'last_name' => 'Duck','user_login' => 'donald','user_pass' => md5('donaldo'),'user_nicename' => 'donald',
            'user_email' => 'donald@example.com','user_url' => '','user_registered' => '2015-12-02',
            'user_activation_key' => '','user_status' => '','display_name' => 'donald',
            'spam' => false,'deleted' => false
        ];
        $wp_user = new WP_User();
        $properties = array_merge($default, $properties);
        foreach ($properties as $property => $value) {
            $wp_user->set($property, $value);
        }
        return $wp_user;
    }

    /**
     * @param $property
     * @param $value
     */
    public function set($property, $value)
    {
        $this->data[$property] = $value;
    }
}
