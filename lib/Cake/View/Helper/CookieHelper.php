<?php
/**
 * Cookie Helper provides access to the Cookie in the Views.
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
 * @package       Cake.View.Helper
 * @since         CakePHP(tm) v 2.3.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');
App::uses('CakeCookie', 'Model/Datasource');

/**
 * Cookie Helper.
 *
 * Cookie reading from the view.
 *
 * @package       Cake.View.Helper
 */
class CookieHelper extends AppHelper {

/**
 * Used to read a cookie values set in a controller for a key or return values for all keys.
 *
 * In your view: `$this->Cookie->read('Controller.cookKey');`
 * Calling the method without a param will return all cookie vars
 *
 * @param string $name the name of the cookie key you want to read
 * @return mixed values from the cookie vars
 */
	public function read($name = null) {
		return CakeCookie::read($name);
	}

/**
 * Used to check is a cookie key has been set
 *
 * In your view: `$this->Cookie->check('Controller.cookKey');`
 *
 * @param string $name
 * @return boolean
 */
	public function check($name) {
		return CakeCookie::check($name);
	}

}
