<?php
/**
 * PHP Version 5.4
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 3.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Cake\Test\TestCase\ORM;

use Cake\Core\Configure;
use Cake\Database\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Used to test correct class is instantiated when using TableRegistry::get();
 */
class MyUsersTable extends Table {

	/**
	 * Overrides default table name
	 *
	 * @var string
	 */
	protected $_table = 'users';

}


/**
 * Test case for TableRegistry
 */
class TableRegistryTest extends TestCase {

	/**
	 * setup
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		Configure::write('App.namespace', 'TestApp');
	}

	/**
	 * tear down
	 *
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();
		TableRegistry::clear();
	}

	/**
	 * Test config() method.
	 *
	 * @return void
	 */
	public function testConfig() {
		$this->assertEquals([], TableRegistry::config('Test'));

		$data = [
			'connection' => 'testing',
			'entityClass' => 'TestApp\Model\Entity\Article',
		];
		$result = TableRegistry::config('Test', $data);
		$this->assertEquals($data, $result, 'Returns config data.');

		$result = TableRegistry::config();
		$expected = ['Test' => $data];
		$this->assertEquals($expected, $result);
	}

	/**
	 * Test getting instances from the registry.
	 *
	 * @return void
	 */
	public function testGet() {
		$result = TableRegistry::get('Article', [
			'table' => 'my_articles',
		]);
		$this->assertInstanceOf('Cake\ORM\Table', $result);
		$this->assertEquals('my_articles', $result->table());

		$result2 = TableRegistry::get('Article', [
			'table' => 'herp_derp',
		]);
		$this->assertSame($result, $result2);
		$this->assertEquals('my_articles', $result->table());
	}

	/**
	 * Test that get() uses config data set with config()
	 *
	 * @return void
	 */
	public function testGetWithConfig() {
		TableRegistry::config('Article', [
			'table' => 'my_articles',
		]);
		$result = TableRegistry::get('Article');
		$this->assertEquals('my_articles', $result->table(), 'Should use config() data.');
	}

	/**
	 * Tests that tables can be instantiated based on conventions
	 * and using plugin notation
	 *
	 * @return void
	 */
	public function testBuildConvention() {
		$table = TableRegistry::get('article');
		$this->assertInstanceOf('\TestApp\Model\Repository\ArticleTable', $table);
		$table = TableRegistry::get('Article');
		$this->assertInstanceOf('\TestApp\Model\Repository\ArticleTable', $table);

		$table = TableRegistry::get('author');
		$this->assertInstanceOf('\TestApp\Model\Repository\AuthorTable', $table);
		$table = TableRegistry::get('Author');
		$this->assertInstanceOf('\TestApp\Model\Repository\AuthorTable', $table);

		$class = $this->getMockClass('\Cake\ORM\Table');
		$class::staticExpects($this->once())
			->method('defaultConnectionName')
			->will($this->returnValue('test'));

		class_alias($class, 'MyPlugin\Model\Repository\SuperTestTable');
		$table = TableRegistry::get('MyPlugin.SuperTest');
		$this->assertInstanceOf($class, $table);
	}

	/**
	 * Tests that table options can be pre-configured for the factory method
	 *
	 * @return void
	 */
	public function testConfigAndBuild() {
		TableRegistry::clear();
		$map = TableRegistry::config();
		$this->assertEquals([], $map);

		$connection = ConnectionManager::get('test', false);
		$options = ['connection' => $connection];
		TableRegistry::config('users', $options);
		$map = TableRegistry::config();
		$this->assertEquals(['users' => $options], $map);
		$this->assertEquals($options, TableRegistry::config('users'));

		$schema = ['id' => ['type' => 'rubbish']];
		$options += ['schema' => $schema];
		TableRegistry::config('users', $options);

		$table = TableRegistry::get('users', ['table' => 'users']);
		$this->assertInstanceOf('Cake\ORM\Table', $table);
		$this->assertEquals('users', $table->table());
		$this->assertEquals('users', $table->alias());
		$this->assertSame($connection, $table->connection());
		$this->assertEquals(array_keys($schema), $table->schema()->columns());
		$this->assertEquals($schema['id']['type'], $table->schema()->column('id')['type']);

		TableRegistry::clear();
		$this->assertEmpty(TableRegistry::config());

		TableRegistry::config('users', $options);
		$table = TableRegistry::get('users', ['className' => __NAMESPACE__ . '\MyUsersTable']);
		$this->assertInstanceOf(__NAMESPACE__ . '\MyUsersTable', $table);
		$this->assertEquals('users', $table->table());
		$this->assertEquals('users', $table->alias());
		$this->assertSame($connection, $table->connection());
		$this->assertEquals(array_keys($schema), $table->schema()->columns());
	}

	/**
	 * Test setting an instance.
	 *
	 * @return void
	 */
	public function testSet() {
		$mock = $this->getMock('Cake\ORM\Table');
		$this->assertSame($mock, TableRegistry::set('Article', $mock));
		$this->assertSame($mock, TableRegistry::get('Article'));
	}

}
