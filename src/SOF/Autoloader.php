<?php
namespace SOF;
/**
 * Autoloads SOF classes
 *
 * @author    Stas Kuryan <stas.kuryan at gmail dot com>
 * @license   MIT License
 */
class Autoloader
{
    private $directory;

    public function __construct($dir = null)
    {
        if (is_null($dir)) {
            $dir = dirname(__FILE__) . '/..';
        }
        $this->directory = $dir;
    }

    /**
     * Registers SOF\Autoloader as an SPL autoloader.
     */
    public static function register($dir = null)
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self($dir), 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class A class name.
     *
     * @return boolean Returns true if the class has been loaded
     */
    public function autoload($class)
    {
        if (0 !== strpos($class, 'SOF')) {
            return;
        }
        if (file_exists($file = $this->directory . '/' . str_replace('\\', '/', $class) . '.php')) {
            require $file;
        }
    }
}