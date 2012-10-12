<?php
/**
 * Cookie Component
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
 * @package       Cake.Controller.Component
 * @since         CakePHP(tm) v 1.2.0.4213
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Component', 'Controller');
App::uses('CakeCookie', 'Model/Datasource');

/**
 * Cookie Component.
 *
 * Cookie handling for the controller.
 *
 * @package       Cake.Controller.Component
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/cookie.html
 *
 */
class CookieComponent extends Component {

/**
 * Write a value to the $_COOKIE[$key];
 *
 * Optional [Name.], required key, optional $value, optional $encrypt, optional $expires
 * $this->Cookie->write('[Name.]key, $value);
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
	public function write($key, $value = null, $encrypt = true, $expires = null) {
		CakeCookie::write($key, $value, $encrypt, $expires);
	}

/**
 * Read the value of the $_COOKIE[$key];
 *
 * Optional [Name.], required key
 * $this->Cookie->read(Name.key);
 *
 * @param string $key Key of the value to be obtained. If none specified, obtain map key => values
 * @return string or null, value for specified key
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/cookie.html#CookieComponent::read
 */
	public function read($key = null) {
		return CakeCookie::write($key);
	}

/**
 * Returns true if given variable is set in cookie.
 *
 * @param string $var Variable name to check for
 * @return boolean True if variable is there
 */
	public function check($key = null) {
		return CakeCookie::check($name);
	}

/**
 * Delete a cookie value
 *
 * Optional [Name.], required key
 * $this->Cookie->read('Name.key);
 *
 * You must use this method before any output is sent to the browser.
 * Failure to do so will result in header already sent errors.
 *
 * @param string $key Key of the value to be deleted
 * @return void
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/cookie.html#CookieComponent::delete
 */
	public function delete($key) {
		return CakeCookie::delete($name);
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
	public function destroy() {
		CakeCookie::destroy();
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
 * `$this->Cookie->cookie()`
 *
 * ### Getting a certain cookie configuration
 *
 * `$this->Cookie->cookie('MyCookie')`
 *
 * ### Setting a cookie configuration
 *
 * `$this->Cookie->cookie((array)$options)`
 *
 * @return mixed
 */
	public static function cookie($options = null) {
		return CakeCookie::cookie($options);
	}

/**
 * Send the cookies. This is done automatically by CakeResponse::send()
 *
 * @return void
 */
	public static function send($reset = true) {
		CakeCookie::send($reset);
	}

/**
 * Will allow overriding default encryption method. Use this method
 * in ex: AppController::beforeFilter() before you have read or
 * written any cookies.
 *
 * @param string $type Encryption method
 * @return void
 */
	public function type($type = 'cipher') {
		CakeCookie::type($type);
	}

}

