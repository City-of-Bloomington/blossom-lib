<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Blossom\Classes\Block;
use Blossom\Classes\Template;

include './configuration.inc';

class BlockTest extends PHPUnit_Framework_TestCase
{
    private $template;

    public function __construct() { $this->template = new Template('test', 'html'); }

	public function testVars()
	{
		$block = new Block('test', ['test'=>'something']);
		$this->assertEquals('something', $block->test);

		$block->one = 'another';
		$this->assertEquals('another', $block->one);
	}

	public function testNormalRendering()
	{
		$block = new Block('normal.inc');

		$expectedOutput = file_get_contents(APPLICATION_HOME.'/blocks/html/normal.inc');
		$this->assertEquals($expectedOutput, $block->render('html', $this->template));

		$block = new Block('normal_includes.inc');
		$this->assertEquals($expectedOutput, $block->render('html', $this->template));
	}

	public function testThemeRendering()
	{
        $block = new Block('overridden.inc');

        $expectedOutput = file_get_contents(SITE_HOME.'/Themes/Test/blocks/html/overridden.inc');
        $this->assertEquals($expectedOutput, $block->render('html', $this->template));

        $block = new Block('theme_includes.inc');
        $this->assertEquals($expectedOutput, $block->render('html', $this->template));
	}

	public function testBlossomRendering()
	{
        $block = new Block('about.txt');

        $expectedOutput = file_get_contents(BLOSSOM.'/blocks/html/about.txt');
        $this->assertEquals($expectedOutput, $block->render('html', $this->template));
	}
}
