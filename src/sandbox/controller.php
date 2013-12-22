<?php

namespace sandbox;

use core\storage\Box;

class Controller
{
    protected $box;
    protected $box2;
    protected $box3;
    protected $cont2;
    
    public function setBox3(Box $box3)
    {
        $this->box3 = $box3;
    }
    
    public function getBox()
    {
        return $this->box;
    }
}

?>