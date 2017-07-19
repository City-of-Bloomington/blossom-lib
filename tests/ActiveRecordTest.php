<?php
/**
 * @copyright 2014-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Blossom\Classes\ActiveRecord;

class ActiveRecordTest extends PHPUnit_Framework_TestCase
{
	private $testModel;

	public function __construct()
	{
		$this->testModel = new TestModel();
	}

	public function testGetAndSet()
	{
		$this->testModel->set('testField', 'testValue');
		$this->assertEquals('testValue', $this->testModel->get('testField'));
	}


	public function testForeignKeyObject()
	{
		$this->testModel->setTestModel(new TestModel(1));
		$o = $this->testModel->getTestModel();
		$this->assertEquals(1, $o->get('id'));
	}
}

class TestModel extends Blossom\Classes\ActiveRecord
{
	protected $foreignkey;

	public function __construct($id=null)
	{
		if ($id) { parent::set('id', $id); }
	}

	public function validate() { }

	public function getId() { return parent::get('id'); }

	public function get($field)  { return parent::get($field); }
	public function set($field, $value) { parent::set($field, $value); }

	public function getTestModel()
	{
		return parent::getForeignKeyObject('TestModel', 'foreignkey_id');
	}

	public function setTestModel(TestModel $o)
	{
		parent::setForeignKeyObject('TestModel', 'foreignkey_id', $o);
	}
}
