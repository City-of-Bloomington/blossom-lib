<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Blossom\Classes\Block;
use Blossom\Classes\Template;

class TemplateTest extends PHPUnit_Framework_TestCase
{
	public function testVars()
	{
		$template = new Template('default', 'html', ['test'=>'something']);
		$this->assertEquals('something', $template->test);

		$template->one = 'another';
		$this->assertEquals('another', $template->one);
	}

	public function testNormalRendering()
	{
		$template = new Template('test', 'html');

		$expectedOutput = file_get_contents(APPLICATION_HOME.'/templates/html/test.inc');
		$this->assertEquals($expectedOutput, $template->render());

		$helper = $template->getHelper('test');
		$this->assertEquals('something', $helper->test('something'));

		$template = new Template('partials', 'html');
		$expectedOutput = file_get_contents(APPLICATION_HOME.'/templates/html/partials/testPartial.inc');
		$this->assertEquals($expectedOutput, $template->render());
	}

	public function testThemeRendering()
	{
        $template = new Template('test', 'html');
        $block    = new Block('overridden.inc');

        $expectedOutput = file_get_contents(SITE_HOME.'/Themes/'.THEME.'/blocks/html/overridden.inc');
		$this->assertEquals($expectedOutput, $block->render('html', $template));
	}
}
