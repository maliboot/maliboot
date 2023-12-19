<?php

declare(strict_types=1);

namespace MaliBoot\Swagger;

use Doctrine\Common\Annotations\AnnotationReader;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Annotation\Mapping;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Context\ApplicationContext;
use MaliBoot\ApiAnnotation\ApiBody;
use MaliBoot\ApiAnnotation\ApiController;
use MaliBoot\ApiAnnotation\ApiHeader;
use MaliBoot\ApiAnnotation\ApiParam;
use MaliBoot\ApiAnnotation\ApiPath;
use MaliBoot\ApiAnnotation\ApiQuery;
use MaliBoot\ApiAnnotation\ApiRequest;
use MaliBoot\ApiAnnotation\ApiResponse;
use MaliBoot\ApiAnnotation\ApiServer;
use MaliBoot\ApiAnnotation\ApiSuccess;
use MaliBoot\ApiAnnotation\ApiVersion;
use MaliBoot\Dto\AbstractViewObject;
use MaliBoot\Dto\IdVO;
use MaliBoot\Dto\PageVO;

class SwaggerJson
{
    /**
     * @var ConfigInterface|mixed
     */
    public $config;

    /**
     * @var array|mixed
     */
    public array $swagger;

    public $server;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct($server)
    {
        $this->container = ApplicationContext::getContainer();
        $this->config = $this->container->get(ConfigInterface::class);
        $this->logger = $this->container->get(LoggerFactory::class)->get('apicat');
        $this->swagger = $this->config->get('apicat.swagger');
        $this->server = $server;
    }

    public function addPath($className, $methodName)
    {
        $ignores = $this->config->get('annotations.scan.ignore_annotations', []);
        foreach ($ignores as $ignore) {
            AnnotationReader::addGlobalIgnoredName($ignore);
        }
        $classAnnotation = ApiAnnotation::classMetadata($className);
        $controllerAnnotation = $classAnnotation[ApiController::class] ?? null;
        $serverAnnotation = $classAnnotation[ApiServer::class] ?? null;
        $versionAnnotation = $classAnnotation[ApiVersion::class] ?? null;
        $bindServer = $serverAnnotation ? $serverAnnotation->name : $this->config->get('server.servers.0.name');

        $servers = $this->config->get('server.servers');
        $serversName = array_column($servers, 'name');
        if (! in_array($bindServer, $serversName)) {
            throw new \Exception(sprintf('The bind ApiServer name [%s] not found, defined in %s!', $bindServer, $className));
        }

        if ($bindServer !== $this->server) {
            return;
        }

        $methodAnnotations = ApiAnnotation::methodMetadata($className, $methodName);
        $paramAnnotations = $this->getParamAnnotations($methodAnnotations);

        if (! $controllerAnnotation || ! $methodAnnotations) {
            return;
        }

        $responses = [];
        /** @var \MaliBoot\ApiAnnotation\ApiMapping $mapping */
        $mapping = null;
        $consumes = null;
        foreach ($methodAnnotations as $option) {
            if ($option instanceof Mapping) {
                $mapping = $option;
            }
            if ($option instanceof ApiResponse) {
                $responses[] = $option;
            }
        }

        if ($mapping === null) {
            return;
        }

        $tag = $controllerAnnotation->tag ?: $className;
        $this->swagger['tags'][$tag] = [
            'name' => $tag,
            'description' => $controllerAnnotation->description,
        ];

        $path = $mapping->path;
        $prefix = $controllerAnnotation->prefix;
        $tokens = [$versionAnnotation ? $versionAnnotation->version : null, $prefix, $path];
        $tokens = array_map(function ($item) {
            return ltrim($item, '/');
        }, array_filter($tokens));
        $path = '/' . implode('/', $tokens);

        $method = strtolower($mapping->methods[0]);
        $this->swagger['paths'][$path][$method] = [
            'tags' => [$tag],
            'x-apifox-folder' => $tag,
            'summary' => $mapping->summary ?? $mapping->name ?? '',
            'description' => $mapping->description ?? $mapping->name ?? '',
            'operationId' => implode('', array_map('ucfirst', explode('/', $path))) . $mapping->methods[0],
            'parameters' => $this->makeParameters($paramAnnotations),
            'requestBody' => $this->makeRequestBody($paramAnnotations),
            'responses' => $this->makeResponses($responses),
        ];
        if ($consumes !== null) {
            $this->swagger['paths'][$path][$method]['consumes'] = [$consumes];
        }
    }

    public function globalParams(): array
    {
        $confGlobal = $this->config->get('apicat.global', []);
        $globalParams = [];
        foreach ($confGlobal as $in => $items) {
            if (isset($items[0])) {
                foreach ($items as $item) {
                    $globalParams[] = $this->makeApiParamObject($in, $item);
                }
            } else {
                foreach ($items as $name => $rule) {
                    $value = [
                        'in' => $in,
                        'key' => $name,
                        'rule' => $rule,
                    ];
                    $globalParams[] = $this->makeApiParamObject($in, $value);
                }
            }
        }
        return $globalParams;
    }

    public function makeParameters($paramAnnotations)
    {
        $parameters = [];

        $paramAnnotations = array_merge($paramAnnotations, $this->globalParams());
        /* @var \MaliBoot\ApiAnnotation\ApiQuery $annotation */
        foreach ($paramAnnotations as $key => $item) {
            foreach ($item as $annotation) {
                if (! $annotation instanceof ApiQuery) {
                    continue;
                }

                $parameters[$key] = [
                    'in' => $annotation->in,
                    'name' => $key,
                    'description' => $annotation->description ?? '',
                    'type' => $annotation->type,
                    'required' => $annotation->required ?? false,
                    'example' => $annotation->example ?? '',
                    'default' => $annotation->default ?? '',
                ];

                if (! empty($annotation->ref)) {
                    $parameters[$key]['$ref'] = '#/components/schemas/' . $this->getDefinitionName($annotation->ref);
                }
            }
        }

        return array_values($parameters);
    }

    public function makeRequestBody($paramAnnotations)
    {
        $properties = [];

        $paramAnnotations = array_merge($paramAnnotations, $this->globalParams());
        /* @var \MaliBoot\ApiAnnotation\ApiBody $annotation */
        foreach ($paramAnnotations as $key => $item) {
            foreach ($item as $annotation) {
                if (! $annotation instanceof ApiBody) {
                    continue;
                }

                $properties[$key] = [
                    'type' => $annotation->type,
                    'description' => $annotation->description ?? $annotation->name ?? '',
                    'default' => $annotation->default ?? '',
                ];

                if (! empty($annotation->ref)) {
                    $properties[$key]['$ref'] = '#/components/schemas/' . $this->getDefinitionName($annotation->ref);
                }
            }
        }

        if (empty($properties)) {
            return [];
        }

        return [
            'content' => [
                'application/json' => [
                    'schema' => [
                        'type' => 'object',
                        'properties' => $properties,
                    ],
                ],
            ],
        ];
    }

    public function putFile(string $file, string $content)
    {
        $pathInfo = pathinfo($file);
        if (! empty($pathInfo['dirname'])) {
            if (file_exists($pathInfo['dirname']) === false) {
                if (mkdir($pathInfo['dirname'], 0644, true) && chmod($pathInfo['dirname'], 0644)) {
                    return false;
                }
            }
        }
        return file_put_contents($file, $content);
    }

    public function save()
    {
        $this->swagger['tags'] = array_values($this->swagger['tags'] ?? []);
        $outputFile = $this->config->get('apicat.output_file');
        if (! $outputFile) {
            $this->logger->error('/config/autoload/apicat.php need set output_file');
            return;
        }
        $outputFile = str_replace('{server}', $this->server, $outputFile);
        $this->putFile($outputFile, json_encode($this->swagger, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->logger->debug('Generate swagger.json success!');
    }

    protected function makeResponses($responses)
    {
        $resp = [];
        /** @var ApiResponse $item */
        foreach ($responses as $item) {
            $resp[$item->statusCode] = [
                'description' => $item->description ?? '成功',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'code' => [
                                    'type' => 'integer',
                                ],
                                'msg' => [
                                    'type' => 'string',
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            // 处理PageList<String, Object>
            if (is_string($item->data)
                && strpos($item->data, 'VO') !== false
                && $this->container->has($item->data)) {
                $VOInstance = $this->container->get($item->data);
                if ($VOInstance instanceof PageVO || (! empty($item->type) && $item->type === 'page')) {
                    $data = [
                        'type' => 'object',
                        'properties' => [
                            'page' => [
                                'type' => 'object',
                                'properties' => [
                                    'pageSize' => [
                                        'type' => 'integer',
                                    ],
                                    'pageIndex' => [
                                        'type' => 'integer',
                                    ],
                                    'totalCount' => [
                                        'type' => 'integer',
                                    ],
                                ],
                            ],
                            'list' => [
                                'type' => 'array',
                                'items' => [
                                    '$ref' => '#/components/schemas/' . $this->getDefinitionName($item->data),
                                ],
                            ],
                        ],
                    ];
                    $this->schemaToDefinition($item->data);
                } elseif ($VOInstance instanceof IdVO) {
                    $data = [
                        'type' => 'object',
                        'properties' => [
                            'id' => [
                                'type' => 'integer',
                            ],
                        ],
                    ];
                } elseif ($VOInstance instanceof AbstractViewObject) {
                    $data = [
                        '$ref' => '#/components/schemas/' . $this->getDefinitionName($item->data),
                    ];
                    $this->schemaToDefinition($item->data);
                }
            } elseif (is_array($item->data)
                && isset($item->data[0])
                && is_string($item->data[0])
                && strpos($item->data[0], 'VO') !== false
                && $this->container->has($item->data[0])
            ) {
                $VOInstance = $this->container->get($item->data[0]);
                if ($VOInstance instanceof AbstractViewObject) {
                    // 处理List<String, Object>
                    $data = [
                        'type' => 'array',
                        'items' => [
                            '$ref' => '#/components/schemas/' . $this->getDefinitionName($item->data[0]),
                        ],
                    ];
                    $this->schemaToDefinition($item->data);
                }
            } elseif (is_array($item->data) && isset($item->data[0]) && is_int($item->data[0])) {
                // 处理List<Integer>
                $data = [
                    'type' => 'array',
                    'items' => [
                        'type' => 'integer',
                    ],
                ];
            } elseif (is_array($item->data) && isset($item->data[0]) && is_string($item->data[0])) {
                // 处理List<String>
                $data = [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ];
            } else {
                $data = [
                    'type' => 'object',
                    'properties' => [
                    ],
                ];
            }
            $resp[(string) $item->statusCode]['content']['application/json']['schema']['properties']['data'] = $data;
        }

        return $resp;
    }

    protected function schemaToDefinition($schema, $level = 0)
    {
        if (! $schema) {
            return false;
        }

        if (isset($this->swagger['components']['schemas'][$schema])) {
            return true;
        }

        $this->swagger['components']['schemas'][$this->getDefinitionName($schema)]['type'] = 'object';
        $propertyAnnotations = ApiAnnotation::propertyMetadata($schema);
        foreach ($propertyAnnotations ?? [] as $key => $item) {
            foreach ($item as $annotation) {
                if (! $annotation instanceof ApiParam && ! $annotation instanceof ApiSuccess) {
                    continue;
                }

                if (! empty($annotation->ref)) {
                    $property = [
                        '$ref' => '#/components/schemas/' . $this->getDefinitionName($annotation->ref),
                        'description' => $annotation->description ?? '',
                    ];
                    $this->schemaToDefinition($annotation->ref, ++$level);
                } else {
                    $property = [
                        'type' => $annotation->type,
                        'description' => $annotation->description ?? '',
                    ];
                }

                $this->swagger['components']['schemas'][$this->getDefinitionName($schema)]['properties'][$key] = $property;
            }
        }

        return true;
    }

    protected function getDefinitionName(string $ref): string
    {
        return str_replace(['\\', '\\\\'], '', $ref);
    }

    protected function makeApiParamObject($in, $value)
    {
        switch ($in) {
            case 'body':
                return new ApiBody($value);
            case 'query':
                return new ApiQuery($value);
            case 'path':
                return new ApiPath($value);
            case 'header':
                return new ApiHeader($value);
            default:
                return new ApiQuery($value);
        }
    }

    protected function getParamAnnotations(array $methodAnnotations)
    {
        $className = '';
        foreach ($methodAnnotations as $annotation) {
            if (! $annotation instanceof ApiRequest) {
                continue;
            }

            $className = $annotation->request;
        }

        if (empty($className)) {
            return null;
        }

        return ApiAnnotation::propertyMetadata($className) ?? null;
    }
}
