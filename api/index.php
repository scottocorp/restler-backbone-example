<?php

require_once '../lib/core.php';

/*
 * This application can handle update requests to the DB either via a POST'ed PHP page or via an XHR REST call from (for example)
 * an HTML client. The following global variable will be  used to indicate which:
 *
 */
$g_REST_call=true;

/*
 * Luracast's Restler 3.0 is an open source program for creating an HTTP web service API.
 * (www.luracast.com/products/restler)
 * 
 */
require_once 'vendor/restler.php';
use Luracast\Restler\Restler;
use Luracast\Restler\Responder;
use Luracast\Restler\Defaults;

/*
 * There was a need for more complex validation return messages. this showed the way:
 * http://stackoverflow.com/questions/13107318/how-can-i-return-a-data-object-when-throwing-a-restexception
 * In a nutshell, extend the Responder class (that restler uses for giving a structure to the error and success response)
 * like below:
 */
class MyResponder extends Responder
{
	public static $data = null;

	public function formatError($statusCode, $message)
	{
		$r = array(
				'error' => array(
						'code' => $statusCode,
						'message' => $message
				)
		);
		if (isset(self::$data)) {
			$r['data'] = self::$data;
		}
		return $r;
	}
}
Defaults::$responderClass = 'MyResponder';

require_once 'pbo/property.php';

// Restler setup. See the following for details: http://restler3.phpfogapp.com/examples/
$r = new Restler();
$r->addAPIClass('Luracast\\Restler\\Resources'); //this creates resources.json at API Root
$r->addAPIClass('Property');
$r->handle();

?>