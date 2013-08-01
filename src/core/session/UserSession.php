<?php

namespace core\session;

class UserSession implements Session
{
    public function __construct()
    {
        $this->initialize();
    }
    
    private function initialize()
    {
    	session_start();
        $this->setFingerprint();
        if ($this->isStarted())
        {
            $this->checkStatus();
        }
        else // is this really necessary?
        {
            $this->regenerateId();
        }
    }
    
    private function setFingerprint()
    {
        if (!isset($_SESSION['FINGERPRINT']))
        {
            $_SESSION['FINGERPRINT'] = $this->getFingerprint();
        }
    }
    
    /**
     * Unique identifier of a client combined of:
     * - the user agent being which is accessing the page
     * - the IP address from which the user is viewing the current page
     * - extra salt string
     * @return string hashed unique client identifier
     */
    private function getFingerprint()
    {
    	return hash('sha512', SESSION_SALT . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    }
    
    /**
     * Checks whether $_SESSION contains
     * marker of truely started session.
     * @return boolean true when session started marker is set
     */
    public function isStarted()
    {
    	return isset($_SESSION['STARTED']) && $_SESSION['STARTED'];
    }
    
    /**
     * Monitors started session and execute one of actions:
     * - destroy session and redirect to the expired session
     *   location when the session fingerprint is incorrect
     * - update timeout of the current session
     */
    private function checkStatus()
    {
        if (/* $this->isExpired() ||  */!$this->isFingerprintValid())
        {
            $this->destroyAndRedirect();
        }
        
//         if ($this->extended)
//         {
            $this->updateTimeout();
//         }
//         else
//         {
//             $this->updateLastActivity();
//         }
    }
    
//     private function isExpired()
//     {
//         if (isset($_SESSION['LAST_ACTIVITY']))
//         {
//             $duration = time() - $_SESSION['LAST_ACTIVITY'];
//             return $duration > SESSION_DURATION_LIMIT;
//         }
//     }
    
    private function isFingerprintValid()
    {
    	return isset($_SESSION['FINGERPRINT']) && $_SESSION['FINGERPRINT'] == $this->getFingerprint();
    }
    
    /**
     * Destroys started session and
     * redirects to the expired session location.
     */
    public function destroyAndRedirect()
    {
        $this->destroy();
        header('Location: ' . SESSION_END_REDIRECT_LOCATION);
        exit();
    }
    
//     private function updateLastActivity()
//     {
//     	$_SESSION['LAST_ACTIVITY'] = time();
//     }
    
    private function updateTimeout()
    {
        // saves cookie for a given period of time
        if (isset($_COOKIE[session_name()]))
        {
            if ($this->isExtended())
            {
                $expire = time() + SESSION_DURATION_LIMIT_EXTENDED;
                $this->setExtended(true);
            }
            else
            {
                $expire = 0;
            }
            setcookie(session_name(), $_COOKIE[session_name()], $expire, '/');
        }
    }
    
    /**
     * Destroys all data registered to a session altogether with
     * a session cookie used to propagate the session id (default behavior).
     * 
     * @link http://www.php.net/manual/en/function.session-destroy.php
     * @return bool Returns true on success or false on failure.
     */
    public function destroy()
    {
        // Unset all of the session variables.
        $_SESSION = array();
        
        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get('session.use_cookies'))
        {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            setcookie('extended', null, -1);
        }
        
        // Finally, destroy the session.
        session_destroy();
    }
    
    public function getId()
    {
        return session_id();
    }
    
    /**
     * For security reasons the method should be called at
     * the point of login or any time when user is re-authorized.
     * Old session file is replaced by a newly generated one.
     * 
     * @return true on success or false on failure.
     */
    public function regenerateId()
    {
        return session_regenerate_id(true);
    }
    
    /**
     * Starts truely session with:
     * - new session id
     * - updated session timeout
     * - the session started marker
     */
    public function start()
    {
        $this->updateCookieStatus();
    	$_SESSION['STARTED'] = true;
    }
    
    private function updateCookieStatus()
    {
    	$this->regenerateId();
    	
    	$_COOKIE[session_name()] = session_id();
    	
        $this->updateTimeout();
    }
    
    public function __get($key)
    {
        return $_SESSION[$key];
    }
    
    public function __set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    public function __isset($key)
    {
        return isset($_SESSION[$key]);
    }
    
    public function isExtended()
    {
    	return isset($_COOKIE['extended']) && $_COOKIE['extended'];
    }
    
    public function setExtended($extended)
    {
    	setcookie('extended', $extended, time() + SESSION_DURATION_LIMIT_EXTENDED);
    }
}

?>