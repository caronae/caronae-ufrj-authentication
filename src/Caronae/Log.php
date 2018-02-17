<?php

namespace Caronae;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log {
    protected static $instance;

    static public function getLogger()
    {
        if (!self::$instance) {
            self::configureInstance();
        }

        return self::$instance;
    }

    protected static function configureInstance()
    {
        $logger = new Logger('caronae-ufrj-authentication');
        $logger->pushHandler(new StreamHandler(getenv('LOG_STREAM')));
        $logger->pushProcessor(function ($record) {
            $record['extra']['session_id'] = session_id();
            return $record;
        });

        self::$instance = $logger;
    }

    public static function debug($message, array $context = [])
    {
        self::getLogger()->addDebug($message, $context);
    }

    public static function info($message, array $context = [])
    {
        self::getLogger()->addInfo($message, $context);
    }

    public static function notice($message, array $context = [])
    {
        self::getLogger()->addNotice($message, $context);
    }

    public static function warning($message, array $context = [])
    {
        self::getLogger()->addWarning($message, $context);
    }

    public static function error($message, array $context = [])
    {
        self::getLogger()->addError($message, $context);
    }

    public static function critical($message, array $context = [])
    {
        self::getLogger()->addCritical($message, $context);
    }

    public static function alert($message, array $context = [])
    {
        self::getLogger()->addAlert($message, $context);
    }

    public static function emergency($message, array $context = [])
    {
        self::getLogger()->addEmergency($message, $context);
    }

}