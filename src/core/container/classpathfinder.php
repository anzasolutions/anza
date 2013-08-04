<?php

namespace core\container;

class ClasspathFinder
{
    private $paths = array();
    
    public function __construct()
    {
        $this->initialize();
    }
    
    private function initialize()
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(APPLICATION_ROOT_PATH));
        while ($it->valid())
        {
            if (!$it->isDot())
            {
                $classname = substr(strrchr($it->getSubPathName(), DIRECTORY_SEPARATOR), 1, -4);
                $this->paths[$classname] = $it->getSubPath();
            }
            $it->next();
        }
    }
    
    public function find($classname)
    {
        if (isset($this->paths[strtolower($classname)]))
        {
            return $this->paths[$class];
        }
    }
}

?>