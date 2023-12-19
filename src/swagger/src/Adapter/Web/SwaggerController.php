<?php

declare(strict_types=1);

namespace MaliBoot\Swagger\Adapter\Web;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Codec\Json;
use MaliBoot\Cola\Adapter\AbstractController;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

/**
 * @Controller
 */
class SwaggerController extends AbstractController
{
    /**
     * @RequestMapping(path="/swagger/openapi.json", methods={"GET"})
     *
     * @return ResponseInterface
     */
    public function handle(ResponseInterface $response): Psr7ResponseInterface
    {
        if (! config('apicat.enable')) {
            throw new \Exception('404');
        }

        $path = BASE_PATH . '/public/swagger/swagger.json';
        if (! file_exists($path)) {
            throw new \Exception('swagger.json file does not exist!');
        }

        $swaggerJson = file_get_contents($path);
        return $response->json(Json::decode($swaggerJson));
    }
}
