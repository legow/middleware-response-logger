<?php

/*
 * All rights reserved © 2017 Legow Hosting Kft.
 */

declare (strict_types = 1);

namespace LegoW\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Description of LogResponseFactory
 *
 * @author Turcsán Ádám <turcsan.adam@legow.hu>
 */
class LogResponseFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new LogResponse($container->get(LoggerInterface::class));
    }
}
