<?php

declare(strict_types=1);

namespace MaliBoot\Request;

use Hyperf\Contract\Arrayable;
use MaliBoot\Dto\UserContext;
use MaliBoot\Request\Contract\RequestInterface;

class Request extends \Hyperf\HttpServer\Request implements RequestInterface
{
    public function getUser(): ?UserContext
    {
        $user = $this->getAttribute('user', null);
        if (is_null($user)) {
            return $user;
        }
        
        if (! $user instanceof UserContext) {
            $userContext = new UserContext();

            if ($user instanceof Arrayable
                || $user instanceof \MaliBoot\Utils\Contract\Arrayable
                || (is_object($user) && method_exists($user, 'toArray'))
            ) {
                $user = $user->toArray();
            }

            $userContext->initData((array) $user);
        } else {
            $userContext = $user;
        }

        return $userContext;
    }
}