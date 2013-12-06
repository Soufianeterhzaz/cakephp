<?php
/**
 * RequestActionController file
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/view/1196/Testing>
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/view/1196/Testing CakePHP(tm) Tests
 * @since         CakePHP(tm) v 3.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace TestApp\Controller;

/**
 * RequestActionController class
 *
 */
class RequestActionController extends AppController {

/**
 * uses property
 *
 * @var array
 * @access public
 */
	public $uses = ['Post'];

/**
 * test_request_action method
 *
 * @access public
 * @return void
 */
	public function testRequestAction() {
		return 'This is a test';
	}

/**
 * another_ra_test method
 *
 * @param mixed $id
 * @param mixed $other
 * @access public
 * @return void
 */
	public function anotherRaTest($id, $other) {
		return $id + $other;
	}

/**
 * normal_request_action method
 *
 * @return void
 */
	public function normalRequestAction() {
		return 'Hello World';
	}

/**
 * returns $this->here
 *
 * @return void
 */
	public function returnHere() {
		return $this->here;
	}

/**
 * paginate_request_action method
 *
 * @return void
 */
	public function paginateRequestAction() {
		$data = $this->paginate();
		return true;
	}

/**
 * post pass, testing post passing
 *
 * @return array
 */
	public function postPass() {
		return $this->request->data;
	}

/**
 * query pass, testing query passing
 *
 * @return array
 */
	public function queryPass() {
		return $this->request->query;
	}

/**
 * test param passing and parsing.
 *
 * @return array
 */
	public function paramsPass() {
		return $this->request;
	}

	public function paramCheck() {
		$this->autoRender = false;
		$content = '';
		if (isset($this->request->params[0])) {
			$content = 'return found';
		}
		$this->response->body($content);
	}
}
