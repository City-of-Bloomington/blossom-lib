<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Blossom\Classes\Url;

include './configuration.inc';

class UrlTest extends PHPUnit_Framework_TestCase
{

	public function testUrlOutput()
	{
		$testUrl = 'http://www.somewhere.com/test';

		$url = new Url($testUrl);
		$this->assertEquals($testUrl, "$url");
	}

	public function testChangeScheme()
	{
		$url = new Url('http://www.somewhere.com');
		$url->setScheme('webcal://');
		$this->assertEquals('webcal://www.somewhere.com', "$url");
	}

	public function testUrlWithoutScheme()
	{
		$url = new Url('bloomington.in.gov/test');
		$this->assertEquals('http', $url->getScheme(), 'Scheme not set to HTTP');
		$this->assertEquals('http://bloomington.in.gov/test', $url->getScript());
		$this->assertEquals('http://bloomington.in.gov/test', $url->__toString());
	}

	public function testUrlWithPort()
	{
        $url = new Url('ftp://bloomington.in.gov:20');
        $this->assertEquals('ftp://bloomington.in.gov:20', $url->__toString());
	}
}
