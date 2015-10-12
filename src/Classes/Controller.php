<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;

abstract class Controller
{
	protected $template;

	abstract public function index();

	public function __construct(Template &$template)
	{
		$this->template = $template;
		$this->template->controller = get_class($this);
	}

	/**
	 * Returns the full URL for a named route
	 *
	 * This loads the $ROUTES global variable and calls the
	 * generate function on it.
	 *
	 * @see https://github.com/auraphp/Aura.Router/tree/2.x
	 * @param string $route_name
	 * @param array $params
	 * @return string
	 */
	public static function generateUrl($route_name, $params=[])
	{
        global $ROUTES;
        return "$_SERVER[REQUEST_SCHEME]://$_SERVER[SERVER_NAME]".$ROUTES->generate($route_name, $params);
	}
}
