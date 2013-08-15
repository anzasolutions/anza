<?php

namespace core\container;

/**
 * Contains functionality to create, initialize
 * and provide instances of classes across application.
 *
 * @author anza
 */
class Container
{
    private $classes;
    private $objects;
    private $singles;
    private $injections;
    
    public function __construct()
    {
        $this->initialize();
        $this->bind('container', function ()
        {
            return $this;
        });
    }
    
    /**
     * Recursively walks through a project structure.
     * Based on the paths and class files found creates
     * registry of classes to be used for instantiation.
     */
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
        $this->injections = simplexml_load_file(INJECT_FILE);
    }
    
    /**
     * Creates new instance of a registered class.
     *
     * @param string $key a reference to a registered container class
     * @throws CreateException when impossible to create new object
     * @return object properly created instance of registered class
     */
    public function create($key)
    {
        if (isset($this->objects[$key]))
        {
            $object = $this->objects[$key]();
            return $this->inject($key, $object);
        }
        throw new CreateException("Cannot create instance of a class by the given key: $key");
    }
    
    /**
     * Provides a single shared instance of a registered class.
     *
     * @param string $key a reference to a registered container class
     * @return object an instance shared across application
     */
    public function single($key)
    {
        if (isset($this->singles[$key]))
        {
            return $this->singles[$key];
        }
        return $this->singles[$key] = $this->create($key);
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
            $fields = $this->injections->xpath('//class[@name = "' . $key . '"]/field[@name = "' . $property->name . '"]');
            foreach ($fields as $field)
            {
                $method = (string) $field['single'] == 'true' ? 'single' : 'create';
                $dependency = $this->{$method}((string) $field['type']);
                
                if ($reflection->hasMethod($setter = 'set' . $property->name))
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
     * for later creation as a normal instance or a single.
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