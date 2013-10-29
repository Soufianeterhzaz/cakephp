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
namespace Cake\Database\Dialect;

use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\OrderByExpression;
use Cake\Database\Expression\UnaryExpression;
use Cake\Database\Query;
use Cake\Database\SqlDialectTrait;

/**
 * Contains functions that encapsulates the SQL dialect used by Postgres,
 * including query translators and schema introspection.
 */
trait PostgresDialectTrait {

	use SqlDialectTrait;

	/**
	 *  String used to start a database identifier quoting to make it safe
	 *
	 * @var string
	 */
	protected $_startQuote = '"';

	/**
	 * String used to end a database identifier quoting to make it safe
	 *
	 * @var string
	 */
	protected $_endQuote = '"';

	/**
	 * Returns a query that has been transformed to the specific SQL dialect
	 * by changing or re-arranging SQL clauses as required.
	 *
	 * @param Cake\Database\Query $query
	 * @return Cake\Database\Query
	 */
	protected function _selectQueryTranslator($query) {
		return $query;
	}

	/**
	 * Returns an dictionary of expressions to be transformed when compiling a Query
	 * to SQL. Array keys are method names to be called in this class
	 *
	 * @return array
	 */
	protected function _expressionTranslators() {
		$namespace = 'Cake\Database\Expression';
		return [
			$namespace . '\FunctionExpression' => '_transformFunctionExpression'
		];
	}

	/**
	 * Receives a FunctionExpression and changes it so that it conforms to this
	 * SQL dialect.
	 *
	 * @param Cake\Database\Expression\FunctionExpression
	 * @return void
	 */
	protected function _transformFunctionExpression(FunctionExpression $expression) {
		switch ($expression->name()) {
			case 'CONCAT':
				// CONCAT function is expressed as exp1 || exp2
				$expression->name('')->type(' ||');
				break;
			case 'DATEDIFF':
				$expression
					->name('')
					->type('-')
					->iterateParts(function($p) {
						return new FunctionExpression('DATE', [$p['value']], [$p['type']]);
					});
				break;
			case 'CURRENT_DATE':
				$time = new FunctionExpression('LOCALTIMESTAMP', [' 0 ' => 'literal']);
				$expression->name('CAST')->type(' AS ')->add([$time, 'date' => 'literal']);
				break;
			case 'CURRENT_TIME':
				$time = new FunctionExpression('LOCALTIMESTAMP', [' 0 ' => 'literal']);
				$expression->name('CAST')->type(' AS ')->add([$time, 'time' => 'literal']);
				break;
			case 'NOW':
				$expression->name('LOCALTIMESTAMP')->add([' 0 ' => 'literal']);
				break;
		}
	}

	/**
	 * Get the schema dialect.
	 *
	 * Used by Cake\Schema package to reflect schema and
	 * generate schema.
	 *
	 * @return Cake\Database\Schema\PostgresSchema
	 */
	public function schemaDialect() {
		return new \Cake\Database\Schema\PostgresSchema($this);
	}
}
