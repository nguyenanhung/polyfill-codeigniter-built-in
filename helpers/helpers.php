<?php
if (!function_exists('is_windows')) {
    /**
     * Check if we're running on a Windows platform
     */
    function is_windows()
    {
        return DIRECTORY_SEPARATOR === '\\';
    }
}
if (!function_exists('is_https')) {
    /**
     * Is HTTPS?
     *
     * Determines if the application is accessed via an encrypted (HTTPS) connection.
     *
     * @return    bool
     */
    function is_https()
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            return true;
        }

        if (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }

        return false;
    }
}
if (!function_exists('is_cli')) {

    /**
     * Is CLI?
     *
     * Test to see if a request was made from the command line.
     *
     * @return    bool
     */
    function is_cli()
    {
        return (PHP_SAPI === 'cli' or defined('STDIN'));
    }
}
if (!function_exists('__value__')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function __value__($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}
if (!function_exists('__startsWith__')) {
    function __startsWith__($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}
if (!function_exists('__endsWith__')) {
    function __endsWith__($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === mb_substr($haystack, -mb_strlen($needle))) {
                return true;
            }
        }

        return false;
    }
}
if (!function_exists('__uri_string__')) {
    function __uri_string__($uri)
    {
        if (__env__('ENABLE_QUERY_STRINGS') === false) {
            is_array($uri) && $uri = implode('/', $uri);

            return ltrim($uri, '/');
        }

        if (is_array($uri)) {
            return http_build_query($uri);
        }

        return $uri;
    }
}
if (!function_exists('__env__')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function __env__($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return __value__($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (strlen($value) > 1 && __startsWith__($value, '"') && __endsWith__($value, '"')) {
            return mb_substr($value, 1, -1);
        }

        return $value;
    }
}
if (!function_exists('log_message')) {
    function log_message($level, $message)
    {
        return \nguyenanhung\Polyfill\CodeIgniter\Polyfill\Log::write($level, $message);
    }
}
if (!function_exists('base_url')) {
    function base_url($uri = '', $protocol = null)
    {
        if (function_exists('url')) {
            // Trường hợp sử dụng Laravel
            $secure = $protocol === 'https' ? true : null;

            return url('/' . $uri, [], $secure);
        }
        $base_url = __env__('APP_URL');
        if (empty($base_url)) {
            if (isset($_SERVER['SERVER_ADDR'])) {
                if (strpos($_SERVER['SERVER_ADDR'], ':') !== false) {
                    $server_addr = '[' . $_SERVER['SERVER_ADDR'] . ']';
                } else {
                    $server_addr = $_SERVER['SERVER_ADDR'];
                }
                $base_url = (is_https() ? 'https' : 'http') . '://' . $server_addr . substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], basename($_SERVER['SCRIPT_FILENAME'])));
            } else {
                $base_url = 'http://localhost/';
            }
        }
        $base_url = rtrim($base_url, '/') . '/';
        if (isset($protocol)) {
            // For protocol-relative links
            if ($protocol === '') {
                $base_url = substr($base_url, strpos($base_url, '//'));
            } else {
                $base_url = $protocol . substr($base_url, strpos($base_url, '://'));
            }
        }

        return $base_url . __uri_string__($uri);
    }
}
if (!function_exists('site_url')) {
    function site_url($uri = '', $protocol = null)
    {
        if (function_exists('url')) {
            // Trường hợp sử dụng Laravel
            $secure = $protocol === 'https' ? true : null;

            return url('/' . $uri, [], $secure);
        }
        $base_url = __env__('APP_URL');
        $index_page = __env__('INDEX_PAGE');
        if (isset($protocol)) {
            // For protocol-relative links
            if ($protocol === '') {
                $base_url = substr($base_url, strpos($base_url, '//'));
            } else {
                $base_url = $protocol . substr($base_url, strpos($base_url, '://'));
            }
        }
        if (empty($uri)) {
            return $base_url . $index_page;
        }
        $uri = __uri_string__($uri);
        if (__env__('ENABLE_QUERY_STRINGS') === false) {
            if (!empty(__env__('URL_SUFFIX'))) {
                $suffix = __env__('URL_SUFFIX');
            } else {
                $suffix = '';
            }

            if ($suffix !== '') {
                if (($offset = strpos($uri, '?')) !== false) {
                    $uri = substr($uri, 0, $offset) . $suffix . substr($uri, $offset);
                } else {
                    $uri .= $suffix;
                }
            }
            $index_page = rtrim($index_page, '/') . '/';

            return $base_url . $index_page . $uri;
        }

        if (strpos($uri, '?') === false) {
            $uri = '?' . $uri;
        }

        return $base_url . $index_page . $uri;
    }
}
