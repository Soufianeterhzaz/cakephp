<?php
/**
 * CookieHelperTest file
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.Test.Case.View.Helper
 * @since         CakePHP(tm) v 1.2.0.4206
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('CookieHelper', 'View/Helper');

/**
 * CookieHelperTest class
 *
 * @package       Cake.Test.Case.View.Helper
 */
class CookieHelperTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$controller = null;
		$this->View = new View($controller);
		$this->Cookie = new CookieHelper($this->View);

		$_COOKIE = array();
		CakeCookie::write('test', 'info');
		CakeCookie::write('Deeply', array('nested' => array('key' => 'value')));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		$_COOKIE = array();
		unset($this->View, $this->Cookie);
		CakePlugin::unload();
		parent::tearDown();
	}

/**
 * testRead method
 *
 * @return void
 */
	public function testRead() {
		$result = $this->Cookie->read('Deeply.nested.key');
		$this->assertEquals('value', $result);

		$result = $this->Cookie->read('test');
		$this->assertEquals('info', $result);
	}

/**
 * testCheck method
 *
 * @return void
 */
	public function testCheck() {
		$this->assertTrue($this->Cookie->check('test'));

		$this->assertTrue($this->Cookie->check('Deeply.nested.key'));

		$this->assertFalse($this->Cookie->check('Does.not.exist'));

		$this->assertFalse($this->Cookie->check('Nope'));
	}

}
