<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Blossom\Classes\View;

class ViewTest extends PHPUnit_Framework_TestCase
{
	public function testVars()
	{
		$view = new ViewStub(['test'=>'something']);
		$this->assertEquals('something', $view->test);

		$view->one = 'another test';
		$this->assertEquals('another test', $view->one);
	}

	public function testEscaping()
	{
        $input    = "one <>&' two";
        $expected = "one &lt;&gt;&amp;&#039; two";

        $escaped   = View::escape($input);
        $unescaped = View::unescape($escaped);
        $this->assertEquals($escaped, $expected);
        $this->assertEquals($unescaped, $input);
	}

	public function testDateFormatConversion()
	{
        $date_format = 'n/j/Y';

        $newFormat = View::convertDateFormat($date_format, 'mysql');
        $this->assertEquals('%c/%e/%Y', $newFormat);
	}
}

class ViewStub extends View
{
	public function __construct($vars=null)
	{
		parent::__construct($vars);
	}

	public function render() { return 'test content'; }
}
