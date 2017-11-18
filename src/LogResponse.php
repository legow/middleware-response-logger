<?php

/*
 * All rights reserved © 2017 Legow Hosting Kft.
 */

declare (strict_types = 1);

namespace LegoW\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Description of LogResponse
 *
 * @author Turcsán Ádám <turcsan.adam@legow.hu>
 */
class LogResponse implements MiddlewareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        $response = $delegate($request);
        if ($response instanceof ResponseInterface) {
            $this->logger->log(\Psr\Log\LogLevel::INFO, $this->composeHttpMessageFromResponse($response));
        }
        return $response;
    }

    private function composeHttpMessageFromResponse(ResponseInterface $response): string
    {
        $httpMessage = 'HTTP/' . $response->getProtocolVersion();
        $httpMessage .= ' ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . PHP_EOL;
        foreach ($response->getHeaders() as $name => $values) {
            $httpMessage .= $name . ": " . implode(", ", $values);
        }
        if ($this->needsContentLength($response)) {
            $httpMessage .= 'Content-Length: ' . $this->getContentLength($response);
        }
        $httpMessage .= PHP_EOL;
        $httpMessage .= $response->getBody()->getContents();
        return $httpMessage;
    }

    private function needsContentLength(ResponseInterface $response): bool
    {
        return ! $response->hasHeader('Content-Length');
    }

    private function getContentLength(ResponseInterface $respone): int
    {
        $size = $respone->getBody()->getSize();
        if (! empty($size)) {
            return $size;
        }

        $content = $respone->getBody()->getContents();
        $contentLength = strlen($content);
        return $contentLength;
    }
}
