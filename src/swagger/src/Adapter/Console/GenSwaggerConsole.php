<?php

declare(strict_types=1);

namespace MaliBoot\Swagger\Adapter\Console;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\HttpServer\Router\Handler;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Str;
use MaliBoot\Swagger\SwaggerJson;

/**
 * Class SwaggerGenCommand.
 */
class GenSwaggerConsole extends HyperfCommand
{
    protected ?string $name = 'plugin:gen-swagger';

    public function handle()
    {
        $container = ApplicationContext::getContainer();
        $logger = $container->get(LoggerFactory::class)->get('swagger');
        $config = $container->get(ConfigInterface::class);

        if (! $config->get('swagger.enable')) {
            $logger->debug('swagger not enable');
            return;
        }
        $output = $config->get('swagger.output_file');
        if (! $output) {
            $logger->error('/config/autoload/swagger.php need set output_file');
            return;
        }
        $servers = $config->get('server.servers');
        if (count($servers) > 1 && ! Str::contains($output, '{server}')) {
            $logger->warning('You have multiple serve, but your swagger.output_file not contains {server} var');
        }
        foreach ($servers as $server) {
            if ($server['type'] != \Hyperf\Server\Server::SERVER_HTTP) {
                continue;
            }
            $router = $container->get(DispatcherFactory::class)->getRouter($server['name']);
            $data = $router->getData();
            $swagger = new SwaggerJson($server['name']);

            array_walk_recursive($data, function ($item) use ($swagger) {
                if ($item instanceof Handler && ! ($item->callback instanceof \Closure)) {
                    [$controller, $action] = $this->prepareHandler($item->callback);
                    $swagger->addPath($controller, $action);
                }
            });

            $swagger->save();
        }

        $this->info('Swagger generated successfully!');
    }

    protected function prepareHandler($handler): array
    {
        if (is_string($handler)) {
            if (strpos($handler, '@') !== false) {
                return explode('@', $handler);
            }
            return explode('::', $handler);
        }
        if (is_array($handler) && isset($handler[0], $handler[1])) {
            return $handler;
        }
        throw new \RuntimeException('Handler not exist.');
    }
}
