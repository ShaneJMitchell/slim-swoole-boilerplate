<?php

class HttpServer
{
    public static $instance;
    public static $get;
    public static $post;
    public static $header;
    public static $server;

    /**
     * HttpServer constructor.
     */
    public function __construct()
    {
        $http = new Swoole\Http\Server('0.0.0.0', 8888);
        $http->set(
            [
                'worker_num'    => 5,
                'daemonize'     => false,
                'max_request'   => 10,
                'dispatch_mode' => 1,
            ]
        );

        $http->on('WorkerStart', [$this, 'onWorkerStart']);

        $http->on('request', function ($request, $response) {
            register_shutdown_function([$this, 'handleFatal']);

            if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
                return $response->end();
            }

            $this->response = $response;

            if (isset($request->server)) {
                HttpServer::$server = $request->server;
                foreach ($request->server as $key => $value) {
                    $_SERVER[strtoupper($key)] = $value;
                }
            }

            if (isset($request->header)) {
                HttpServer::$header = $request->header;
            }

            if (isset($request->get)) {
                HttpServer::$get = $request->get;
                foreach ($request->get as $key => $value) {
                    $_GET[$key] = $value;
                }
            }

            if (isset($request->post)) {
                HttpServer::$post = $request->post;
                foreach ($request->post as $key => $value) {
                    $_POST[$key] = $value;
                }
            }
            ob_start();

            try {
                // Load package dependencies
                require __DIR__ . '/../vendor/autoload.php';

                // Load Environment variables form .env file
                (Dotenv\Dotenv::create(substr(__DIR__, 0, strrpos(__DIR__, '/'))))->load();
                // To get any environment variable anywhere in the code-base just use getenv('APP_ENV')

                // Create App setting the env for later use
                $app = new \Slim\App();

                // Add all services to the App container
                include __DIR__ . '/../config/services.php';
                // Register routes
                include __DIR__ . '/../config/routes.php';

                $app->run();
            } catch (Exception $e) {
                var_dump($e);
            }

            $result = ob_get_contents();
            ob_end_clean();
            $response->end($result);

            unset($result, $app);
        });

        $http->start();
    }

    /**
     * Fatal Error
     */
    public function handleFatal()
    {
        $error = error_get_last();

        if (!isset($error['type'])) return;

        switch ($error['type']) {
            case E_ERROR:
            case E_PARSE:
            case E_DEPRECATED:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
                break;
            default:
                return;
        }

        $message = $error['message'];
        $file    = $error['file'];
        $line    = $error['line'];
        $log     = "\n$message ($file:$line)\nStack trace:\n";
        $trace   = debug_backtrace(1);

        foreach ($trace as $i => $t) {
            if (!isset($t['file'])) $t['file'] = 'unknown';
            if (!isset($t['line'])) $t['line'] = 0;
            if (!isset($t['function'])) $t['function'] = 'unknown';

            $log .= "#$i {$t['file']}({$t['line']}): ";

            if (isset($t['object']) && is_object($t['object'])) $log .= get_class($t['object']) . '->';

            $log .= "{$t['function']}()\n";
        }

        if (isset($_SERVER['REQUEST_URI'])) $log .= '[QUERY] ' . $_SERVER['REQUEST_URI'];

        if ($this->response) {
            $this->response->status(500);
            $this->response->end($log);
        }
        unset($this->response);
    }

    public function onWorkerStart()
    {
        require __DIR__ . '/../vendor/autoload.php';
        session_start();
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

HttpServer::getInstance();