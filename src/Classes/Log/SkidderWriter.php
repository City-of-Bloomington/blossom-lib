<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Blossom\Classes\Log;

use Zend\Log\Writer\AbstractWriter;

class SkidderWriter extends AbstractWriter
{
    protected $url;
    protected $application_id;

    public function __construct($url, $application_id=null)
    {
        if ($url instanceof Traversable) {
            $url = iterator_to_array($url);
        }

        if (is_array($url)) {
            parent::__construct($url);
            $url = isset($url['url']) ? $url['url'] : null;
            $application_id = isset($url['application_id']) ? $url['application_id'] : null;
        }

        if (!isset($application_id)) {
            throw new Exception\InvalidArgumentException('You must specify a skidder application_id for this application');
        }

        $this->url = $url;
        $this->application_id = $application_id;

        if (!$this->hasFormatter()) {
            $this->setFormatter(new SkidderFormatter());
        }
    }

    public function doWrite(array $event)
    {
        $post = [
            'application_id' => $this->application_id,
            'script'  => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'],
            'type'    => $event['message'],
            'message' => $this->formatter->format($event)
        ];

        $skidder = curl_init($this->url);
        curl_setopt($skidder, CURLOPT_POST,           true);
        curl_setopt($skidder, CURLOPT_HEADER,         true);
        curl_setopt($skidder, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($skidder, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($skidder, CURLOPT_POSTFIELDS, $post);
        curl_exec($skidder);
   }
}