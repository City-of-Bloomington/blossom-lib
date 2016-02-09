<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Blossom\Classes\Log;

use Zend\Log\Formatter\Base;

class SkidderFormatter extends Base
{
    /**
     * Formats data into a string to be written by the writer.
     *
     * @param array $event event data
     * @return string formatted line to write to the log
     */
    public function format($event)
    {
        return print_r($event, true);
    }
}