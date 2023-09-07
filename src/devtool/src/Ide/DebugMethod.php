<?php

namespace MaliBoot\Devtool\Ide;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[Controller]
class DebugMethod extends AbstractHttp
{
    #[RequestMapping(path: "/devtool/ide/debug/method", methods: "POST")]
    public function index(RequestInterface $request)
    {
        $classFQN = $request->input('classFQN');
        $methodName = $request->input('methodName');
        $parameters = $request->input('parameters');
        dump($request->all());
        if (!$classFQN || !$methodName || !$parameters) {
            return $this->response('请求错误', 400);
        }

        try {
            $methodReflection = new \ReflectionMethod($classFQN, $methodName);
        } catch (\ReflectionException) {
            return $this->response($classFQN.'.'.$methodName.'不存在', 500);
        }
        if (!$methodReflection->isPublic()) {
            $methodReflection->setAccessible(true);
        }

        try {
            $applyParams = [];
            foreach ($parameters as $paramter) {
                if (!isset($paramter['name']) || !isset($paramter['type']) || !isset($paramter['value'])) {
                    continue;
                }
                if ($paramter['type'][0] == '\\') {
                    if (!class_exists($paramter['type'])) {
                        return $this->response($paramter['type'].'不存在', 500);
                    }
                    if (method_exists($paramter['type'], 'of')) {
                        $applyParams[] = $paramter['type']::of($paramter['value']);
                        continue;
                    }
                    if (is_array($paramter['value'])) {
                        return $this->response($paramter['type'].'解析错误，其值需要为Map类型', 500);
                    }
                    $applyParams[] = new $paramter['type'](...$paramter['value']);
                    continue;
                }
                $applyParams[] = $paramter['value'];
            }

            $classIns = \Hyperf\Support\make($classFQN);
            $response = $methodReflection->invoke($classIns, ...$applyParams);
            if (is_object($response) && method_exists($response, '__toString')) {
                return $this->response((string) $response);
            }
            if (is_array($response)) {
                return $this->response(json_encode($response));
            }

            return $this->response(json_encode([$response]));
        } catch (\Exception $e) {
            return $this->response(sprintf("message:%s\ntrace:%s", $e->getMessage(), $e->getTraceAsString()), 500);
        }
    }
}