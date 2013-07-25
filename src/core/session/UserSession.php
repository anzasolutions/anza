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
    }
    
    private function setFingerprint()
    {
        if (!isset($_SESSION['FINGERPRINT']))
        {
            $_SESSION['FINGERPRINT'] = $this->getFingerprint();
        }
    }
    
    private function getFingerprint()
    {
    	return hash('sha512', $_SERVER['HTTP_USER_AGENT']);
    }
    
    public function isStarted()
    {
    	return isset($_SESSION['STARTED']) && $_SESSION['STARTED'];
    }
    
    private function checkStatus()
    {
        if ($this->isExpired() || !$this->isValid())
        {
            $this->destroyAndRedirect();
        }
        $this->updateLastActivity();
    }
    
    private function isExpired()
    {
        if (isset($_SESSION['LAST_ACTIVITY']))
        {
            $duration = time() - $_SESSION['LAST_ACTIVITY'];
            return $duration > SESSION_DURATION_LIMIT;
        }
    }
    
    private function isValid()
    {
    	return isset($_SESSION['FINGERPRINT']) && $_SESSION['FINGERPRINT'] == $this->getFingerprint();
    }
    
    public function destroyAndRedirect()
    {
        $this->destroy();
        header('Location: ' . SESSION_END_REDIRECT_LOCATION);
        exit();
    }
    
    private function updateLastActivity()
    {
    	$_SESSION['LAST_ACTIVITY'] = time();
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
    
    public function start()
    {
        $this->regenerateId();
    	$_SESSION['STARTED'] = true;
    }
}

?>