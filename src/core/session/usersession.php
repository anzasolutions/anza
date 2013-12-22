<?php

namespace core\session;

/**
 * Handles user session and keeps track of session data.
 *
 * Expires when:
 * - not extended session cookie is set:
 *   + after a normal expiration limit
 *   + on browser shutdown
 * - extended session cookie is set
 *   + after an extended expiration limit
 * - explicit session destruction by user
 *
 * Is protected from hijack attempts by regeneration of session id
 * and client recognition using a fingerprint id.
 *
 * @author anza
 *
 */
class UserSession implements Session
{
    public function __construct()
    {
        // specific method is kept here only for clarity in
        // constructor and info about purpose of the initialize() body
        $this->initialize();
    }
    
    private function initialize()
    {
        // session must start / resume in order to do anything
        session_start();
        
        // create unique fingerprint of the session client
        // used later for checking whether the currently
        // accessing session user comes from the same source
        $this->setFingerprint();
        
        // quite apparent, but might be confusing as this is not
        // exactly starting session, but only a marker that
        // a user is logged in and therefore in fact an experiencing
        // session activities are allowed
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
        if (!isset($_SESSION['fingerprint']))
        {
            $_SESSION['fingerprint'] = $this->getFingerprint();
        }
    }
    
    /**
     * Unique identifier of a client combined of:
     * - the user agent being which is accessing the page
     * - the IP address from which the user is viewing the current page
     * - extra salt string
     *
     * @return string hashed unique client identifier
     */
    private function getFingerprint()
    {
        return hash('sha512', SESSION_SALT . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    }
    
    /**
     * Checks whether $_SESSION contains
     * marker of truely started session.
     *
     * @return boolean true when session started marker is set
     */
    public function isStarted()
    {
        return isset($_SESSION['started']) && $_SESSION['started'];
    }
    
    /**
     * Monitors started session and execute one of actions:
     * - destroy session and redirect to the expired session
     *   location when the session fingerprint is incorrect
     * - update timeout of the current session
     */
    private function checkStatus()
    {
        if ($this->isExpired() || !$this->isFingerprintValid())
        {
            $this->destroyAndRedirect();
        }
        $this->updateTimeout();
    }
    
    private function isExpired()
    {
        if (!$this->isExtended())
        {
            if (isset($_SESSION['last_activity']))
            {
                $duration = time() - $_SESSION['last_activity'];
                return $duration > SESSION_DURATION_LIMIT;
            }
        }
    }
    
    private function isExtended()
    {
        return isset($_COOKIE['extended']) && $_COOKIE['extended'];
    }
    
    private function isFingerprintValid()
    {
        return isset($_SESSION['fingerprint']) && $_SESSION['fingerprint'] == $this->getFingerprint();
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
    
    private function updateTimeout()
    {
        if (isset($_COOKIE[session_name()]))
        {
            if ($this->isExtended())
            {
                $expire = time() + SESSION_DURATION_LIMIT_EXTENDED;
                $this->setExtended();
            }
            else
            {
                $expire = 0;
                $this->updateLastActivity();
            }
            setcookie(session_name(), $_COOKIE[session_name()], $expire, '/');
        }
    }
    
    private function updateLastActivity()
    {
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Destroys all data registered to a session altogether with
     * a session cookies used to propagate the session id (default behavior).
     * Extended session cookie is destroyed as well.
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
    
    /**
     * @deprecated
     */
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
     * Starts user session with:
     * - new session id
     * - updated session timeout
     * - the session started marker
     */
    public function start()
    {
        $this->updateCookieStatus();
        $_SESSION['started'] = true;
    }
    
    private function updateCookieStatus()
    {
        // session id regeration for security reasons
        $this->regenerateId();
        
        // new session id is assigned to the existing session cookie
        $_COOKIE[session_name()] = session_id();
        
        // session expiration timeout is updated
        $this->updateTimeout();
    }
    
    public function __get($key)
    {
        if (isset($_SESSION[$key]))
        {
            return $_SESSION[$key];
        }
    }
    
    public function __set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    public function setExtended()
    {
        setcookie('extended', true, time() + SESSION_DURATION_LIMIT_EXTENDED);
    }
}

?>