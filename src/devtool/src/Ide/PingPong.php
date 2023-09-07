<?php

namespace MaliBoot\Devtool\Ide;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[Controller]
class PingPong extends AbstractHttp
{
    #[RequestMapping(path: "/devtool/ide/ping", methods: "GET,OPTIONS")]
    public function index(RequestInterface $request)
    {
        return $this->response('pong');
    }
}