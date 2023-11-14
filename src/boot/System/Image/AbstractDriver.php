<?php

namespace Boot\System\Image;

use Boot\System\Image\Exception\NotSupportedException;

abstract class AbstractDriver
{
    /**
     * Decoder instance to init images from
     *
     * @var \Boot\System\Image\AbstractDecoder
     */
    public $decoder;

    /**
     * Image encoder instance
     *
     * @var \Boot\System\Image\AbstractEncoder
     */
    public $encoder;

    /**
     * Creates new image instance
     *
     * @param  int     $width
     * @param  int     $height
     * @param  string  $background
     * @return \Boot\System\Image\Image
     */
    abstract public function newImage($width, $height, $background);

    /**
     * Reads given string into color object
     *
     * @param  string $value
     * @return AbstractColor
     */
    abstract public function parseColor($value);

    /**
     * Checks if core module installation is available
     *
     * @return boolean
     */
    abstract protected function coreAvailable();

    /**
     * Returns clone of given core
     *
     * @return mixed
     */
    public function cloneCore($core)
    {
        return clone $core;
    }

    /**
     * Initiates new image from given input
     *
     * @param  mixed $data
     * @return \Boot\System\Image\Image
     */
    public function init($data)
    {
        return $this->decoder->init($data);
    }

    /**
     * Encodes given image
     *
     * @param  Image   $image
     * @param  string  $format
     * @param  int     $quality
     * @return \Boot\System\Image\Image
     */
    public function encode($image, $format, $quality)
    {
        return $this->encoder->process($image, $format, $quality);
    }

    /**
     * Executes named command on given image
     *
     * @param  Image  $image
     * @param  string $name
     * @param  array $arguments
     * @return \Boot\System\Image\Commands\AbstractCommand
     */
    public function executeCommand($image, $name, $arguments)
    {
        $commandName = $this->getCommandClassName($name);
        $command = new $commandName($arguments);
        $command->execute($image);

        return $command;
    }

    /**
     * Returns classname of given command name
     *
     * @param  string $name
     * @return string
     */
    private function getCommandClassName($name)
    {
        if (extension_loaded('mbstring')) {
            $name = mb_strtoupper(mb_substr($name, 0, 1)) . mb_substr($name, 1);
        } else {
            $name = strtoupper(substr($name, 0, 1)) . substr($name, 1);
        }

        $drivername = $this->getDriverName();
        $classnameLocal = sprintf('\Boot\System\Image\%s\Commands\%sCommand', $drivername, ucfirst($name));
        $classnameGlobal = sprintf('\Boot\System\Image\Commands\%sCommand', ucfirst($name));

        if (class_exists($classnameLocal)) {
            return $classnameLocal;
        } elseif (class_exists($classnameGlobal)) {
            return $classnameGlobal;
        }

        throw new NotSupportedException(
            "Command ({$name}) is not available for driver ({$drivername})."
        );
    }

    /**
     * Returns name of current driver instance
     *
     * @return string
     */
    public function getDriverName()
    {
        $reflect = new \ReflectionClass($this);
        $namespace = $reflect->getNamespaceName();

        return substr(strrchr($namespace, "\\"), 1);
    }
}
