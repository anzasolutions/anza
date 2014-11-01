<?php

namespace app\login;

class LoginController
{
    private $post;
    private $session;
    
    public function login()
    {
        if ($this->post->login == 'andy')
        {
            if ($this->post->remember)
            {
                $this->session->setExtended();
            }
            $this->session->start();
            
            $this->session->login = 'andy';
            $this->session->roles = array (
                    'author' );
            
            header ( 'Location: ' . SESSION_END_REDIRECT_LOCATION );
        }
    }
    
    public function destroy()
    {
        $this->session->destroyAndRedirect ();
    }
}

?>