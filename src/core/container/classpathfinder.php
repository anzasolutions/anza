<?php

namespace core\container;

class ClasspathFinder
{
    private $paths;
    private $objects;
    private $singletons;
    
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
                $namespace = $it->getSubPath();

//                 $this->objects[$classname] = function () use ($namespace, $classname)
                $this->objects[$classname] = function ($singleton = false) use ($namespace, $classname)
                {
                    $class = $namespace . '\\' . $classname;
                    return new $class();
                };
            }
            $it->next();
        }
        print_r($this->paths);
//         print_r($this->objects);

//         $ob = $this->objects['box']();
//         $ob->lolo = 'koko';
//         echo $ob->lolo;
        
//         print_r($ob);
//         $g = $this->find('noinstanceincontainerexception') . '\\' . 'noinstanceincontainerexception';
//         $get = new $g();
//         print_r($get);
    }
    
    public function find($classname)
    {
        $classname = strtolower($classname);
        if (isset($this->paths[$classname]))
        {
            return $this->paths[$classname];
        }
    }
    
//     public function get($classname)
//     {
//         $classname = strtolower($classname);
//         if (isset($this->objects[$classname]))
//         {
//             return $this->objects[$classname]();
//         }
//     }
    
    public function get($classname, $singleton = false)
    {
        $classname = strtolower($classname);
        
        if ($singleton)
        {
            if (isset($this->singletons[$classname]))
            {
                return $this->singletons[$classname];
            }
            return $this->singletons[$classname] = $this->objects[$classname]($singleton);
        }
        
        if (isset($this->objects[$classname]))
        {
            return $this->objects[$classname]();
        }
    }
}

?>