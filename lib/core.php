<?php

set_include_path(get_include_path() . PATH_SEPARATOR . '/set/the/correct/path');

// Error handling
require_once 'lib/error.php';
// Session handling
require_once 'lib/session.php' ;
// Base Class
require_once 'pbo/base.php' ;

/*
 * This application can handle update requests to the DB either via a POST'ed PHP page or via an XHR REST call from (for example)
 * an HTML client. The following global variable will be  used to indicate which:
 * 
 */ 
$g_REST_call=false;


?>