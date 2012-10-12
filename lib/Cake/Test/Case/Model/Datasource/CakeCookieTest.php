<?php
/**
 * CookieTest file
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
 * @package       Cake.Test.Case.Model.Datasource
 * @since         CakePHP(tm) v 1.2.0.4206
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeCookie', 'Model/Datasource');

class TestCakeCookie extends CakeCookie {

	public static function reset() {
		self::$_started = false;
	}

}

/**
 * CakeCookieTest class
 *
 * @package       Cake.Test.Case.Model.Datasource
 */
class CakeCookieTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$_COOKIE = array();

		TestCakeCookie::$name = 'CakeTestCookie';
		TestCakeCookie::$time = 10;
		TestCakeCookie::$path = '/';
		TestCakeCookie::$domain = '';
		TestCakeCookie::$secure = false;
		TestCakeCookie::$key = 'somerandomhaskey';

		Configure::write('Cookie', array(
		));
		TestCakeCookie::init();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function teardown() {
		unset($_COOKIE);
		parent::teardown();
	}

/**
 * test setting ini properties with Cookie configuration.
 *
 * @return void
 */
	public function _testCookieConfigIniSetting() {
		$_Cookie = null;

		Configure::write('Cookie', array(
			'cookie' => 'test',
			'checkAgent' => false,
			'timeout' => 86400,
			'ini' => array(
				'Cookie.referer_check' => 'example.com',
				'Cookie.use_trans_sid' => false
			)
		));
		TestCakeCookie::start();
		$this->assertEquals('', ini_get('Cookie.use_trans_sid'), 'Ini value is incorrect');
		$this->assertEquals('example.com', ini_get('Cookie.referer_check'), 'Ini value is incorrect');
		$this->assertEquals('test', ini_get('Cookie.name'), 'Ini value is incorrect');
	}


/**
 * sets up some default cookie data.
 *
 * @return void
 */
	protected function _setCookieData() {
		TestCakeCookie::write(array('Encrytped_array' => array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!')));
		TestCakeCookie::write(array('Encrytped_multi_cookies.name' => 'CakePHP'));
		TestCakeCookie::write(array('Encrytped_multi_cookies.version' => '1.2.0.x'));
		TestCakeCookie::write(array('Encrytped_multi_cookies.tag' => 'CakePHP Rocks!'));

		TestCakeCookie::write(array('Plain_array' => array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!')), null, false);
		TestCakeCookie::write(array('Plain_multi_cookies.name' => 'CakePHP'), null, false);
		TestCakeCookie::write(array('Plain_multi_cookies.version' => '1.2.0.x'), null, false);
		TestCakeCookie::write(array('Plain_multi_cookies.tag' => 'CakePHP Rocks!'), null, false);
	}

/**
 * test that initialize sets settings from components array
 *
 * @return void
 */
	public function testSettings() {
		$settings = array(
			'time' => '5 days',
			'path' => '/'
		);
		TestCakeCookie::init();
		$this->assertEquals(TestCakeCookie::$time, $settings['time']);
		$this->assertEquals(TestCakeCookie::$path, $settings['path']);
	}

/**
 * testCookieName
 *
 * @return void
 */
	public function testCookieName() {
		$this->assertEquals('CakeTestCookie', TestCakeCookie::$name);
	}

/**
 * testReadEncryptedCookieData
 *
 * @return void
 */
	public function testReadEncryptedCookieData() {
		$this->_setCookieData();
		$data = TestCakeCookie::read('Encrytped_array');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Encrytped_multi_cookies');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);
	}

/**
 * testReadPlainCookieData
 *
 * @return void
 */
	public function testReadPlainCookieData() {
		$this->_setCookieData();
		$data = TestCakeCookie::read('Plain_array');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_multi_cookies');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);
	}

/**
 * test read() after switching the cookie name.
 *
 * @return void
 */
	public function testReadWithNameSwitch() {
		$_COOKIE = array(
			'CakeTestCookie' => array(
				'key' => 'value'
			),
			'OtherTestCookie' => array(
				'key' => 'other value'
			)
		);
		$this->assertEquals('value', TestCakeCookie::read('key'));

		TestCakeCookie::$name = 'OtherTestCookie';
		$this->assertEquals('other value', TestCakeCookie::read('key'));
	}

/**
 * test a simple write()
 *
 * @return void
 */
	public function testWriteSimple() {
		TestCakeCookie::write('Testing', 'value');
		$result = TestCakeCookie::read('Testing');

		$this->assertEquals('value', $result);
	}

/**
 * test write with httpOnly cookies
 *
 * @return void
 */
	public function testWriteHttpOnly() {
		TestCakeCookie::$httpOnly = true;
		TestCakeCookie::$secure = false;
		TestCakeCookie::write('Testing', 'value', false);
		$expected = array(
			'name' => TestCakeCookie::$name . '[Testing]',
			'value' => 'value',
			'expire' => time() + 10,
			'path' => '/',
			'domain' => '',
			'secure' => false,
			'httpOnly' => true);
		$result = $this->Controller->response->cookie(TestCakeCookie::$name . '[Testing]');
		$this->assertEquals($expected, $result);
	}

/**
 * test delete with httpOnly
 *
 * @return void
 */
	public function testDeleteHttpOnly() {
		TestCakeCookie::$httpOnly = true;
		TestCakeCookie::$secure = false;
		TestCakeCookie::delete('Testing', false);
		$expected = array(
			'name' => TestCakeCookie::$name . '[Testing]',
			'value' => '',
			'expire' => time() - 42000,
			'path' => '/',
			'domain' => '',
			'secure' => false,
			'httpOnly' => true);
		$result = $this->Controller->response->cookie(TestCakeCookie::$name . '[Testing]');
		$this->assertEquals($expected, $result);
	}

/**
 * testWritePlainCookieArray
 *
 * @return void
 */
	public function testWritePlainCookieArray() {
		TestCakeCookie::write(array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!'), null, false);

		$this->assertEquals('CakePHP', TestCakeCookie::read('name'));
		$this->assertEquals('1.2.0.x', TestCakeCookie::read('version'));
		$this->assertEquals('CakePHP Rocks!', TestCakeCookie::read('tag'));

		TestCakeCookie::delete('name');
		TestCakeCookie::delete('version');
		TestCakeCookie::delete('tag');
	}

/**
 * test writing values that are not scalars
 *
 * @return void
 */
	public function testWriteArrayValues() {
		TestCakeCookie::$secure = false;
		TestCakeCookie::write('Testing', array(1, 2, 3), false);
		$expected = array(
			'name' => TestCakeCookie::$name . '[Testing]',
			'value' => '[1,2,3]',
			'path' => '/',
			'domain' => '',
			'secure' => false,
			'httpOnly' => false);
		$result = $this->Controller->response->cookie(TestCakeCookie::$name . '[Testing]');

		$this->assertWithinMargin($result['expire'], time() + 10, 1);
		unset($result['expire']);
		$this->assertEquals($expected, $result);
	}

/**
 * testReadingCookieValue
 *
 * @return void
 */
	public function testReadingCookieValue() {
		$this->_setCookieData();
		$data = TestCakeCookie::read();
		$expected = array(
			'Encrytped_array' => array(
				'name' => 'CakePHP',
				'version' => '1.2.0.x',
				'tag' => 'CakePHP Rocks!'),
			'Encrytped_multi_cookies' => array(
				'name' => 'CakePHP',
				'version' => '1.2.0.x',
				'tag' => 'CakePHP Rocks!'),
			'Plain_array' => array(
				'name' => 'CakePHP',
				'version' => '1.2.0.x',
				'tag' => 'CakePHP Rocks!'),
			'Plain_multi_cookies' => array(
				'name' => 'CakePHP',
				'version' => '1.2.0.x',
				'tag' => 'CakePHP Rocks!'));
		$this->assertEquals($expected, $data);
	}

/**
 * testDeleteCookieValue
 *
 * @return void
 */
	public function testDeleteCookieValue() {
		$this->_setCookieData();
		TestCakeCookie::delete('Encrytped_multi_cookies.name');
		$data = TestCakeCookie::read('Encrytped_multi_cookies');
		$expected = array('version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);

		TestCakeCookie::delete('Encrytped_array');
		$data = TestCakeCookie::read('Encrytped_array');
		$this->assertNull($data);

		TestCakeCookie::delete('Plain_multi_cookies.name');
		$data = TestCakeCookie::read('Plain_multi_cookies');
		$expected = array('version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);

		TestCakeCookie::delete('Plain_array');
		$data = TestCakeCookie::read('Plain_array');
		$this->assertNull($data);
	}

/**
 * testReadingCookieArray
 *
 * @return void
 */
	public function testReadingCookieArray() {
		$this->_setCookieData();

		$data = TestCakeCookie::read('Encrytped_array.name');
		$expected = 'CakePHP';
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Encrytped_array.version');
		$expected = '1.2.0.x';
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Encrytped_array.tag');
		$expected = 'CakePHP Rocks!';
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Encrytped_multi_cookies.name');
		$expected = 'CakePHP';
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Encrytped_multi_cookies.version');
		$expected = '1.2.0.x';
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Encrytped_multi_cookies.tag');
		$expected = 'CakePHP Rocks!';
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_array.name');
		$expected = 'CakePHP';
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_array.version');
		$expected = '1.2.0.x';
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_array.tag');
		$expected = 'CakePHP Rocks!';
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_multi_cookies.name');
		$expected = 'CakePHP';
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_multi_cookies.version');
		$expected = '1.2.0.x';
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_multi_cookies.tag');
		$expected = 'CakePHP Rocks!';
		$this->assertEquals($expected, $data);
	}

/**
 * testReadingCookieDataOnStartup
 *
 * @return void
 */
	public function testReadingCookieDataOnStartup() {
		$data = TestCakeCookie::read('Encrytped_array');
		$this->assertNull($data);

		$data = TestCakeCookie::read('Encrytped_multi_cookies');
		$this->assertNull($data);

		$data = TestCakeCookie::read('Plain_array');
		$this->assertNull($data);

		$data = TestCakeCookie::read('Plain_multi_cookies');
		$this->assertNull($data);

		$_COOKIE['CakeTestCookie'] = array(
				'Encrytped_array' => $this->_encrypt(array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!')),
				'Encrytped_multi_cookies' => array(
						'name' => $this->_encrypt('CakePHP'),
						'version' => $this->_encrypt('1.2.0.x'),
						'tag' => $this->_encrypt('CakePHP Rocks!')),
				'Plain_array' => '{"name":"CakePHP","version":"1.2.0.x","tag":"CakePHP Rocks!"}',
				'Plain_multi_cookies' => array(
						'name' => 'CakePHP',
						'version' => '1.2.0.x',
						'tag' => 'CakePHP Rocks!'));
		TestCakeCookie::reset();
		TestCakeCookie::init();

		$data = TestCakeCookie::read('Encrytped_array');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Encrytped_multi_cookies');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_array');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_multi_cookies');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);
		TestCakeCookie::destroy();
		unset($_COOKIE['CakeTestCookie']);
	}

/**
 * testReadingCookieDataWithoutStartup
 *
 * @return void
 */
	public function testReadingCookieDataWithoutStartup() {
		$data = TestCakeCookie::read('Encrytped_array');
		$expected = null;
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Encrytped_multi_cookies');
		$expected = null;
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_array');
		$expected = null;
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_multi_cookies');
		$expected = null;
		$this->assertEquals($expected, $data);

		$_COOKIE['CakeTestCookie'] = array(
				'Encrytped_array' => $this->_encrypt(array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!')),
				'Encrytped_multi_cookies' => array(
						'name' => $this->_encrypt('CakePHP'),
						'version' => $this->_encrypt('1.2.0.x'),
						'tag' => $this->_encrypt('CakePHP Rocks!')),
				'Plain_array' => '{"name":"CakePHP","version":"1.2.0.x","tag":"CakePHP Rocks!"}',
				'Plain_multi_cookies' => array(
						'name' => 'CakePHP',
						'version' => '1.2.0.x',
						'tag' => 'CakePHP Rocks!'));

		$data = TestCakeCookie::read('Encrytped_array');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Encrytped_multi_cookies');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_array');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);

		$data = TestCakeCookie::read('Plain_multi_cookies');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!');
		$this->assertEquals($expected, $data);
		TestCakeCookie::destroy();
		unset($_COOKIE['CakeTestCookie']);
	}

/**
 * Test Reading legacy cookie values.
 *
 * @return void
 */
	public function testReadLegacyCookieValue() {
		$_COOKIE['CakeTestCookie'] = array(
			'Legacy' => array('value' => $this->_oldImplode(array(1, 2, 3)))
		);
		$result = TestCakeCookie::read('Legacy.value');
		$expected = array(1, 2, 3);
		$this->assertEquals($expected, $result);
	}

/**
 * Test reading empty values.
 */
	public function testReadEmpty() {
		$_COOKIE['CakeTestCookie'] = array(
			'JSON' => '{"name":"value"}',
			'Empty' => '',
			'String' => '{"somewhat:"broken"}'
		);
		$this->assertEquals(array('name' => 'value'), TestCakeCookie::read('JSON'));
		$this->assertEquals('value', TestCakeCookie::read('JSON.name'));
		$this->assertEquals('', TestCakeCookie::read('Empty'));
		$this->assertEquals('{"somewhat:"broken"}', TestCakeCookie::read('String'));
	}

/**
 * test that no error is issued for non array data.
 *
 * @return void
 */
	public function testNoErrorOnNonArrayData() {
		TestCakeCookie::destroy();
		$_COOKIE['CakeTestCookie'] = 'kaboom';

		$this->assertNull(TestCakeCookie::read('value'));
	}

/**
 * testCheck method
 *
 * @return void
 */
	public function testCheck() {
		TestCakeCookie::write('CookieComponentTestCase', 'value');
		$this->assertTrue(TestCakeCookie::check('CookieComponentTestCase'));

		$this->assertFalse(TestCakeCookie::check('NotExistingCookieComponentTestCase'));
	}

/**
 * testCheckingSavedEmpty method
 *
 * @return void
 */
	public function testCheckingSavedEmpty() {
		TestCakeCookie::write('CookieComponentTestCase', 0);
		$this->assertTrue(TestCakeCookie::check('CookieComponentTestCase'));

		TestCakeCookie::write('CookieComponentTestCase', '0');
		$this->assertTrue(TestCakeCookie::check('CookieComponentTestCase'));

		TestCakeCookie::write('CookieComponentTestCase', false);
		$this->assertTrue(TestCakeCookie::check('CookieComponentTestCase'));

		TestCakeCookie::write('CookieComponentTestCase', null);
		$this->assertFalse(TestCakeCookie::check('CookieComponentTestCase'));
	}

/**
 * testCheckKeyWithSpaces method
 *
 * @return void
 */
	public function testCheckKeyWithSpaces() {
		TestCakeCookie::write('CookieComponent Test', "test");
		$this->assertTrue(TestCakeCookie::check('CookieComponent Test'));
		TestCakeCookie::delete('CookieComponent Test');

		TestCakeCookie::write('CookieComponent Test.Test Case', "test");
		$this->assertTrue(TestCakeCookie::check('CookieComponent Test.Test Case'));
	}

/**
 * testCheckEmpty
 *
 * @return void
 */
	public function testCheckEmpty() {
		$this->assertFalse(TestCakeCookie::check());
	}

/**
 * test that deleting a top level keys kills the child elements too.
 *
 * @return void
 */
	public function testDeleteRemovesChildren() {
		$_COOKIE['CakeTestCookie'] = array(
			'User' => array('email' => 'example@example.com', 'name' => 'mark'),
			'other' => 'value'
		);
		$this->assertEquals('mark', TestCakeCookie::read('User.name'));

		TestCakeCookie::delete('User');
		$this->assertNull(TestCakeCookie::read('User.email'));
		TestCakeCookie::destroy();
	}

/**
 * Test deleting recursively with keys that don't exist.
 *
 * @return void
 */
	public function testDeleteChildrenNotExist() {
		$this->assertNull(TestCakeCookie::delete('NotFound'));
		$this->assertNull(TestCakeCookie::delete('Not.Found'));
	}

/**
 * Helper method for generating old style encoded cookie values.
 *
 * @return string.
 */
	protected function _oldImplode(array $array) {
		$string = '';
		foreach ($array as $key => $value) {
			$string .= ',' . $key . '|' . $value;
		}
		return substr($string, 1);
	}

/**
 * Implode method to keep keys are multidimensional arrays
 *
 * @param array $array Map of key and values
 * @return string String in the form key1|value1,key2|value2
 */
	protected function _implode(array $array) {
		return json_encode($array);
	}

/**
 * encrypt method
 *
 * @param array|string $value
 * @return string
 */
	protected function _encrypt($value) {
		if (is_array($value)) {
			$value = $this->_implode($value);
		}
		return "Q2FrZQ==." . base64_encode(Security::cipher($value, TestCakeCookie::$key));
	}

}
