<?php
/**
 * Project polyfill-codeigniter-built-in
 * Created by PhpStorm
 * User: 713uk13m <dev@nguyenanhung.com>
 * Copyright: 713uk13m <dev@nguyenanhung.com>
 * Date: 22/07/2022
 * Time: 00:20
 */

namespace nguyenanhung\Polyfill\CodeIgniter\Polyfill;

class Log
{
    public static function write($level = 'info', $message = '')
    {
        $level  = strtolower($level);
        $logger = new \nguyenanhung\MyDebug\Logger();
        $logger->setDebugStatus(true)
               ->setLoggerPath(__env__('LOGGER_PATH'))
               ->setLoggerFilename('Log-' . date('Y-m-d') . '.log');

        return $logger->$level($message);
    }
}
