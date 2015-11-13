<?php

namespace Application\Application\Service;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\ServiceManager\ServiceLocatorInterface;

class SystemLog
{
    protected $svLocator;
    protected $logger;
    protected $filename = 'system.log';
    const LOG_DIR = 'var/log';

    public function __construct(ServiceLocatorInterface $svLocator, $filename = null) {
        $this->svLocator = $svLocator;
        if (null !=  $filename) {
            $this->filename = $filename;
        }
        $writer = new Stream(HOME_DIR . '/' . self::LOG_DIR . '/' . $this->filename );
        $this->logger = new  Logger();
        $this->logger->addWriter($writer);
    }

    public function changeFileName($filename) {
        $this->filename = $filename;
        $writer = new Stream(HOME_DIR . '/' . self::LOG_DIR . '/' . $this->filename);
        $this->logger = new  Logger();
        $this->logger->addWriter($writer, 2);

    }
    public function __get($name) {
        if ($name == 'logger') {
            return $this->$name;
        }
        throw new \Exception('プロパティーが定義されていません');
    }

    public function log($priority, $message, $extra = array())
    {
        if (is_array($message) || is_object($message)) {
            ob_start();
            $message = print_r($message, true);
            ob_get_clean();
        }
        $this->logger->log($priority, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function emerg($message, $extra = array())
    {
        return $this->log($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function alert($message, $extra = array())
    {
        return $this->log(Logger::ALERT, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function crit($message, $extra = array())
    {
        return $this->log(Logger::CRIT, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function err($message, $extra = array())
    {
        return $this->log(Logger::ERR, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function warn($message, $extra = array())
    {
        return $this->log(Logger::WARN, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function notice($message, $extra = array())
    {
        return $this->log(Logger::NOTICE, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function info($message, $extra = array())
    {
        return $this->log(Logger::INFO, $message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function debug($message, $extra = array())
    {
        return $this->log(Logger::DEBUG, $message, $extra);
    }
}