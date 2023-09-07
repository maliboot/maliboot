<?php

namespace MaliBoot\Devtool\Adapter\Ide;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[Controller]
class PingPong extends AbstractIDE
{
    #[RequestMapping(path: "/devtool/ide/ping", methods: "GET,OPTIONS")]
    public function index(RequestInterface $request)
    {
        return $this->response('pong');
    }
}