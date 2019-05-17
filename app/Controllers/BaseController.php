<?php

namespace App\Controllers;

use \Slim\Http\Request;
use \Slim\Http\Response;

/**
 * Class BaseController
 */
class BaseController
{
    protected $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array|null $args
     * @return Response
     */
    public function index(Request $request, Response $response, $args)
    {
        return $response->write('SUCCESS!');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array|null $args
     * @return Response
     */
    public function greetings(Request $request, Response $response, $args)
    {
//        $this->logger->addInfo('testing info');
//        $this->logger->addDebug('testing debug');
//        $this->logger->addWarning('testing warning');
//        $this->logger->addError('testing error');
//        $this->logger->addCritical('testing critical');

        return $response->write("Hello, " . $args['name']);
    }
}
