<?php

namespace core\container;

class Container
{
    private $classes;
    private $objects;
    private $singletons;
    private $injections;
    
    public function __construct()
    {
        $this->initialize();
        $this->injections = simplexml_load_file(INJECT_FILE);
    }
    
    private function initialize()
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(APPLICATION_ROOT_PATH));
        foreach ($it as $path)
        {
            if (!$it->isDot())
            {
                $namespace = $it->getSubPath();
                $classname = $path->getBasename('.php');
                $class = $namespace . '\\' . $classname;
                $this->classes[$classname] = $class;
                $this->objects[$classname] = function () use($class)
                {
                    return new $class();
                };
            }
        }
    }
    
    public function create($key)
    {
        if (isset($this->objects[$key]))
        {
            $object = $this->objects[$key]();
            return $this->inject($key, $object);
        }
        throw new CreateException("Cannot create instance of a class by the given key: $key");
    }
    
    public function singleton($key)
    {
        if (isset($this->singletons[$key]))
        {
            return $this->singletons[$key];
        }
        return $this->singletons[$key] = $this->create($key);
    }
    
    /**
     * Injects shared or new instances of dependency classes
     * to the given object based on reflection and injection
     * configuration.
     * 
     * @param string $key a reference to a registered container class
     * @param object $object target of injections
     * @throws IncorrectInjectionSupertypeException
     * @return object an instance with fields injected
     */
    public function inject($key, $object)
    {
        $reflection = new \ReflectionObject($object);
        $properties = $reflection->getProperties();
        foreach ($properties as $property)
        {
            $fields = $this->injections->xpath('//class[@name = "' . $key . '"]/field[@name = "'. $property->name . '"]');
            foreach ($fields as $field)
            {
                $method = (string) $field['singleton'] == 'true' ? 'singleton' : 'create';
                $dependency = $this->{$method}((string) $field['type']);
                
                if ($reflection->hasMethod($setter = 'set'.$property->name))
                {
                    $object->$setter($dependency);
                }
                else
                {
                    if (isset($field['supertype']))
                    {
                        $super = $this->classes[(string) $field['supertype']];
                        if (!($dependency instanceof $super))
                        {
                            throw new IncorrectInjectionSupertypeException("{$field['type']} is not an instance of $super");
                        }
                    }
                    $property->setAccessible(true);
                    $property->setValue($object, $dependency);
                }
            }
        }
        return $object;
    }
    
    /**
     * Explicitly binds class to a key in the Container
     * for later creation as a normal instance or a singleton.
     * 
     * @param string $key a reference to a registered container class
     * @param object $closure code to be executed on invocation
     */
    public function bind($key, $closure)
    {
        $this->objects[$key] = $closure;
    }
}

?>