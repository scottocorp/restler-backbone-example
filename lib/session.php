<?php
/**
 *=-----------------------------------------------------------=
 * session.inc
 *=-----------------------------------------------------------=
 *
 * This file will contain the core session handling code that
 * we will use in our simple blog web application.  We will
 * do a few extra things when we create a session to try and
 * avoid some common security problems:
 *
 * 1. We will add a 'created' variable to the session data to
 *    verify that we were the ones who created this session
 *    (this helps avoid session fixation attacks).
 *
 * 2. We will record a hash of the client's USER_AGENT string
 *    along with another string to reduce the chance of a
 *    compromised session id being used successfully.
 */
define('USER_AGENT_SALT', 'restler-backbone-example');


/**
 *=-----------------------------------------------------------=
 * nuke_session
 *=-----------------------------------------------------------=
 * This function completely destroys a session and all of its
 * data after we have logged a user out of our system.  In
 * addition to destroying the session data, we destroy the session
 * cookie and also make sure that $_SESSION is unset.
 */
function nuke_session()
{
  session_destroy();
  setcookie(session_name(), '', time() - 3600);
  $_SESSION[] = array();
}

/**
 * One of these sessions can last 60 minutes
 */
ini_set('session.gc_maxlifetime', 3600);
session_start();


/**
 * Try to prevent session fixation by ensuring that we created
 * the session id.
 */
if (!isset($_SESSION['created']))
{
  session_regenerate_id();
  $_SESSION['created'] = TRUE;
}

/**
 * Try to limit the damage from a compromised session id by
 * saving a hash of the User-Agent: string with another
 * value.
 */
if (!isset($_SESSION['user_agent']))
{
  /**
   * create a hash user agent and a string to store in session
   * data and user cookies
   */
  $_SESSION['user_agent'] =
      md5($_SERVER['HTTP_USER_AGENT'] . USER_AGENT_SALT);
  setcookie('ag', $_SESSION['user_agent'], 0);
}
else
{
  /**
   * verify the user agent matches the session data and
   * cookies.
   */
  if ($_SESSION['user_agent'] !=
          md5($_SERVER['HTTP_USER_AGENT'] . USER_AGENT_SALT)
      or (isset($_COOKIE['ag'])
          and $_COOKIE['ag'] != $_SESSION['user_agent']))
  {
    /**
     * Possible Security Violation.  Tell the user what
     * happened and refuse to continue.
     */
    throw new SessionCompromisedException();
  }
}

?>
