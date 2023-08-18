<?php

declare(strict_types=1);

namespace MaliBoot\Framework\Bootstrap;

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;

class ServerStartCallback extends \Hyperf\Framework\Bootstrap\ServerStartCallback
{
    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function beforeStart()
    {
        $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
        $console->info('MaliBoot Server start success...');
        $console->info($this->welcome());
        str_contains(PHP_OS, 'CYGWIN') && $console->info('current booting the user: ' . shell_exec('whoami'));
    }

    protected function welcome(): string
    {
        return sprintf('
/---------------------- welcome to use --------------------\
|                                                          |
|   __  __           _   _   ____                    _     |  
|  |  \/  |         | | (_) |  _ \                  | |    | 
|  | \  / |   __ _  | |  _  | |_) |   ___     ___   | |_   |
|  | |\/| |  / _` | | | | | |  _ <   / _ \   / _ \  | __|  |
|  | |  | | | (_| | | | | | | |_) | | (_) | | (_) | | |_   |
|  |_|  |_|  \__,_| |_| |_| |____/   \___/   \___/   \__|  |                                                 
|                                                          |
\_____________  Copyright MaliBoot 2022 ~ %s  ___________|
', date('Y'));
    }
}