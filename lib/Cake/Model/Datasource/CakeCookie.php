<?php
/**
 * Cookie class for Cake.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Model.Datasource
 * @since         CakePHP(tm) v 2.3.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Hash', 'Utility');
App::uses('Security', 'Utility');

/**
 * Cookie class for Cake.
 *
 * Cake abstracts the handling of cookies.
 * There are several convenient methods to access cookie information.
 * This class is the implementation of those methods.
 * They are mostly used by the Cookie Component.
 *
 * @package       Cake.Model.Datasource
 */
class CakeCookie {

/**
 * The name of the cookie.
 *
 * @var string
 */
	public static $name = 'CakeCookie';

/**
 * The time a cookie will remain valid.
 *
 * Can be either integer Unix timestamp or a date string.
 *
 * @var mixed
 */
	public static $time = null;

/**
 * Cookie path.
 *
 * The path on the server in which the cookie will be available on.
 * If  public $cookiePath is set to '/foo/', the cookie will only be available
 * within the /foo/ directory and all sub-directories such as /foo/bar/ of domain.
 * The default value is the entire domain.
 *
 * @var string
 */
	public static $path = '/';

/**
 * Domain path.
 *
 * The domain that the cookie is available.
 *
 * To make the cookie available on all subdomains of example.com.
 * Set CakeCookie::domain = '.example.com';
 *
 * @var string
 */
	public static $domain = '';

/**
 * Secure HTTPS only cookie.

 * Indicates that the cookie should only be transmitted over a secure HTTPS connection.
 * When set to true, the cookie will only be set if a secure connection exists.
 *
 * @var boolean
 */
	public static $secure = false;

/**
 * Encryption key.
 *
 * @var string
 */
	public static $key = null;

/**
 * HTTP only cookie
 *
 * Set to true to make HTTP only cookies.  Cookies that are HTTP only
 * are not accessible in Javascript.
 *
 * @var boolean
 */
	public static $httpOnly = false;

/**
 * Values stored in the cookie.
 *
 * Accessed using CakeCookie::read('Name.key');
 *
 * @var string
 */
	protected static $_values = array();

/**
 * Type of encryption to use.
 *
 * Currently two methods are available: cipher and rijndael
 * Defaults to Security::cipher();
 *
 * @var string
 */
	protected static $_type = 'cipher';

/**
 * Used to reset cookie time if $expire is passed to CakeCookie::write()
 *
 * @var string
 */
	protected static $_reset = null;

/**
 * Expire time of the cookie
 *
 * This is controlled by CakeCookie::time;
 *
 * @var string
 */
	protected static $_expires = 0;

/**
 * Holds cookies to be sent to the client
 *
 * @var array
 */
	protected static $_cookies = array();

/**
 * Assert the class is only started once
 *
 * @var boolean
 */
	protected static $_started = false;

/**
 * If encrypted
 *
 * @var boolean
 */
	protected static $_encrypted = null;

/**
 * Pseudo constructor.
 *
 * @return void
 */
	public static function init() {
		if (self::$_started) {
			return;
		}
		self::$_started = true;

		self::$key = Configure::read('Security.salt');
		self::_settings();

		self::_expire(self::$time);
		self::$_values[self::$name] = array();
	}

/**
 * TMP!!!???
 * Allows setting of multiple properties of the object in a single line of code.  Will only set
 * properties that are part of a class declaration.
 *
 * @param array $properties An associative array containing properties and corresponding values.
 * @return void
 */
	protected static function _settings($properties = array()) {
		$settings = (array)Configure::read('Cookie');
		$defaults = array(
			'name' => 'CakeCookie',
			'time' => null,
			'path' => '/',
			'domain' => '',
			'secure' => false,
			'httpOnly' => false
		);
		$settings += $defaults;
  	foreach ($defaults as $key => $val) {
			if (array_key_exists($key, $settings)) {
				self::$$key = $settings[$key];
			}
		}
	}

/**
 * Getter/Setter for cookie configs
 *
 * This method acts as a setter/getter depending on the type of the argument.
 * If the method is called with no arguments, it returns all configurations.
 *
 * If the method is called with a string as argument, it returns either the
 * given configuration if it is set, or null, if it's not set.
 *
 * If the method is called with an array as argument, it will set the cookie
 * configuration to the cookie container.
 *
 * @param $options Either null to get all cookies, string for a specific cookie
 *  or array to set cookie.
 *
 * ### Options (when setting a configuration)
 *  - name: The Cookie name
 *  - value: Value of the cookie
 *  - expire: Time the cookie expires in
 *  - path: Path the cookie applies to
 *  - domain: Domain the cookie is for.
 *  - secure: Is the cookie https?
 *  - httpOnly: Is the cookie available in the client?
 *
 * ## Examples
 *
 * ### Getting all cookies
 *
 * `CakeCookie::cookie()`
 *
 * ### Getting a certain cookie configuration
 *
 * `CakeCookie::cookie('MyCookie')`
 *
 * ### Setting a cookie configuration
 *
 * `CakeCookie::cookie((array)$options)`
 *
 * @return mixed
 */
	public static function cookie($options = null) {
		self::init();
		if ($options === null) {
			return self::$_cookies;
		}

		if (is_string($options)) {
			if (!isset(self::$_cookies[$options])) {
				return null;
			}
			return self::$_cookies[$options];
		}
		self::_set($options);
	}

/**
 * Send the cookies. This is done automatically by CakeResponse::send()
 *
 * @return void
 */
	public static function send($reset = true) {
		foreach (self::$_cookies as $name => $c) {
			setcookie(
				$name, $c['value'], $c['expire'], $c['path'],
				$c['domain'], $c['secure'], $c['httpOnly']
			);
		}
		if ($reset) {
			self::$_cookies = array();
		}
	}

/**
 * Write a value to the $_COOKIE[$key];
 *
 * Optional [Name.], required key, optional $value, optional $encrypt, optional $expires
 * self::$Cookie->write('[Name.]key, $value);
 *
 * By default all values are encrypted.
 * You must pass $encrypt false to store values in clear test
 *
 * You must use this method before any output is sent to the browser.
 * Failure to do so will result in header already sent errors.
 *
 * @param string|array $key Key for the value
 * @param mixed $value Value
 * @param boolean $encrypt Set to true to encrypt value, false otherwise
 * @param integer|string $expires Can be either Unix timestamp, or date string
 * @return void
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/cookie.html#CookieComponent::write
 */
	public static function write($key, $value = null, $encrypt = true, $expires = null) {
		if (empty(self::$_values[self::$name])) {
			self::read();
		}

		if (is_null($encrypt)) {
			$encrypt = true;
		}
		self::$_encrypted = $encrypt;
		self::_expire($expires);

		if (!is_array($key)) {
			$key = array($key => $value);
		}

		foreach ($key as $name => $value) {
			if (strpos($name, '.') === false) {
				self::$_values[self::$name][$name] = $value;
				self::_write("[$name]", $value);
			} else {
				$names = explode('.', $name, 2);
				if (!isset(self::$_values[self::$name][$names[0]])) {
					self::$_values[self::$name][$names[0]] = array();
				}
				self::$_values[self::$name][$names[0]] = Hash::insert(self::$_values[self::$name][$names[0]], $names[1], $value);
				self::_write('[' . implode('][', $names) . ']', $value);
			}
		}
		self::$_encrypted = true;
	}

/**
 * Read the value of the $_COOKIE[$key];
 *
 * Optional [Name.], required key
 * self::$Cookie->read(Name.key);
 *
 * @param string $key Key of the value to be obtained. If none specified, obtain map key => values
 * @return string or null, value for specified key
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/cookie.html#CookieComponent::read
 */
	public static function read($key = null) {
		self::init();
		if (empty(self::$_values[self::$name]) && isset($_COOKIE[self::$name])) {
			self::$_values[self::$name] = self::_decrypt($_COOKIE[self::$name]);
		}
		if (empty(self::$_values[self::$name])) {
			self::$_values[self::$name] = array();
		}
		if (is_null($key)) {
			return self::$_values[self::$name];
		}

		if (strpos($key, '.') !== false) {
			$names = explode('.', $key, 2);
			$key = $names[0];
		}
		if (!isset(self::$_values[self::$name][$key])) {
			return null;
		}

		if (!empty($names[1])) {
			return Hash::get(self::$_values[self::$name][$key], $names[1]);
		}
		return self::$_values[self::$name][$key];
	}

/**
 * Returns true if given variable is set in cookie.
 *
 * @param string $var Variable name to check for
 * @return boolean True if variable is there
 */
	public static function check($key = null) {
		if (empty($key)) {
			return false;
		}
		return self::read($key) !== null;
	}

/**
 * Delete a cookie value
 *
 * Optional [Name.], required key
 * self::$Cookie->read('Name.key);
 *
 * You must use this method before any output is sent to the browser.
 * Failure to do so will result in header already sent errors.
 *
 * @param string $key Key of the value to be deleted
 * @return void
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/cookie.html#CookieComponent::delete
 */
	public static function delete($key) {
		self::init();
		if (empty(self::$_values[self::$name])) {
			self::read();
		}
		if (strpos($key, '.') === false) {
			if (isset(self::$_values[self::$name][$key]) && is_array(self::$_values[self::$name][$key])) {
				foreach (self::$_values[self::$name][$key] as $idx => $val) {
					self::_delete("[$key][$idx]");
				}
			}
			self::_delete("[$key]");
			unset(self::$_values[self::$name][$key]);
			return;
		}
		$names = explode('.', $key, 2);
		if (isset(self::$_values[self::$name][$names[0]])) {
			self::$_values[self::$name][$names[0]] = Hash::remove(self::$_values[self::$name][$names[0]], $names[1]);
		}
		self::_delete('[' . implode('][', $names) . ']');
	}

/**
 * Destroy current cookie
 *
 * You must use this method before any output is sent to the browser.
 * Failure to do so will result in header already sent errors.
 *
 * @return void
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/cookie.html#CookieComponent::destroy
 */
	public static function destroy() {
		self::init();
		if (isset($_COOKIE[self::$name])) {
			self::$_values[self::$name] = self::_decrypt($_COOKIE[self::$name]);
		}

		foreach (self::$_values[self::$name] as $name => $value) {
			if (is_array($value)) {
				foreach ($value as $key => $val) {
					unset(self::$_values[self::$name][$name][$key]);
					self::_delete("[$name][$key]");
				}
			}
			unset(self::$_values[self::$name][$name]);
			self::_delete("[$name]");
		}
	}

/**
 * Will allow overriding default encryption method. Use this method
 * in ex: AppController::beforeFilter() before you have read or
 * written any cookies.
 *
 * @param string $type Encryption method
 * @return void
 */
	public static function type($type = 'cipher') {
		$availableTypes = array(
			'cipher',
			'rijndael'
		);
		if (!in_array($type, $availableTypes)) {
			trigger_error(__d('cake_dev', 'You must use cipher or rijndael for cookie encryption type'), E_USER_WARNING);
			$type = 'cipher';
		}
		self::$_type = $type;
	}

/**
 * Set the expire time for a session variable.
 *
 * Creates a new expire time for a session variable.
 * $expire can be either integer Unix timestamp or a date string.
 *
 * Used by write()
 * CookieComponent::write(string, string, boolean, 8400);
 * CookieComponent::write(string, string, boolean, '5 Days');
 *
 * @param integer|string $expires Can be either Unix timestamp, or date string
 * @return integer Unix timestamp
 */
	protected static function _expire($expires = null) {
		$now = time();
		if (is_null($expires)) {
			return self::$_expires;
		}
		self::$_reset = self::$_expires;

		if ($expires == 0) {
			return self::$_expires = 0;
		}

		if (is_int($expires) || is_numeric($expires)) {
			return self::$_expires = $now + intval($expires);
		}
		return self::$_expires = strtotime($expires, $now);
	}

/**
 * Set cookie
 *
 * @param string $name Name for cookie
 * @param string $value Value for cookie
 * @return void
 */
	protected static function _write($name, $value) {
		self::_set(array(
			'name' => self::$name . $name,
			'value' => self::_encrypt($value),
			'expire' => self::$_expires,
			'path' => self::$path,
			'domain' => self::$domain,
			'secure' => self::$secure,
			'httpOnly' => self::$httpOnly
		));

		if (!is_null(self::$_reset)) {
			self::$_expires = self::$_reset;
			self::$_reset = null;
		}
	}

/**
 * Internally set cookie
 *
 * @param array $options
 * @return void
 */
	protected static function _set(array $options) {
		$defaults = array(
			'name' => 'CakeCookie[default]',
			'value' => '',
			'expire' => 0,
			'path' => '/',
			'domain' => '',
			'secure' => false,
			'httpOnly' => false
		);
		$options += $defaults;

		self::$_cookies[$options['name']] = $options;
	}

/**
 * Sets a cookie expire time to remove cookie value
 *
 * @param string $name Name of cookie
 * @return void
 */
	protected static function _delete($name) {
		self::_set(array(
			'name' => self::$name . $name,
			'value' => '',
			'expire' => time() - 42000,
			'path' => self::$path,
			'domain' => self::$domain,
			'secure' => self::$secure,
			'httpOnly' => self::$httpOnly
		));
	}

/**
 * Encrypts $value using public $type method in Security class
 *
 * @param string $value Value to encrypt
 * @return string encrypted string
 * @return string Encoded values
 */
	protected static function _encrypt($value) {
		if (is_array($value)) {
			$value = self::_implode($value);
		}

		if (self::$_encrypted === true) {
			$type = self::$_type;
			$value = "Q2FrZQ==." . base64_encode(Security::$type($value, self::$key, 'encrypt'));
		}
		return $value;
	}

/**
 * Decrypts $value using public $type method in Security class
 *
 * @param array $values Values to decrypt
 * @return string decrypted string
 */
	protected static function _decrypt($values) {
		$decrypted = array();
		$type = self::$_type;

		foreach ((array)$values as $name => $value) {
			if (is_array($value)) {
				foreach ($value as $key => $val) {
					$pos = strpos($val, 'Q2FrZQ==.');
					$decrypted[$name][$key] = self::_explode($val);

					if ($pos !== false) {
						$val = substr($val, 8);
						$decrypted[$name][$key] = self::_explode(Security::$type(base64_decode($val), self::$key, 'decrypt'));
					}
				}
			} else {
				$pos = strpos($value, 'Q2FrZQ==.');
				$decrypted[$name] = self::_explode($value);

				if ($pos !== false) {
					$value = substr($value, 8);
					$decrypted[$name] = self::_explode(Security::$type(base64_decode($value), self::$key, 'decrypt'));
				}
			}
		}
		return $decrypted;
	}

/**
 * Implode method to keep keys are multidimensional arrays
 *
 * @param array $array Map of key and values
 * @return string A json encoded string.
 */
	protected static function _implode(array $array) {
		return json_encode($array);
	}

/**
 * Explode method to return array from string set in CookieComponent::_implode()
 * Maintains reading backwards compatibility with 1.x CookieComponent::_implode().
 *
 * @param string $string A string containing JSON encoded data, or a bare string.
 * @return array Map of key and values
 */
	protected static function _explode($string) {
		$first = substr($string, 0, 1);
		if ($first === '{' || $first === '[') {
			$ret = json_decode($string, true);
			return ($ret != null) ? $ret : $string;
		}
		$array = array();
		foreach (explode(',', $string) as $pair) {
			$key = explode('|', $pair);
			if (!isset($key[1])) {
				return $key[0];
			}
			$array[$key[0]] = $key[1];
		}
		return $array;
	}

}
