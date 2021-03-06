<?php
/**
 * @copyright 2007-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Blossom\Classes\Block;

if (isset($_SESSION['errorMessages'])) {
	$errorBlock = new Block('errorMessages.inc',array('errorMessages'=>$_SESSION['errorMessages']));
	echo $errorBlock->render($this->outputFormat, $this);
	unset($_SESSION['errorMessages']);
}
