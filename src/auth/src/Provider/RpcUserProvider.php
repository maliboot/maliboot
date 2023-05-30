<?php

declare(strict_types=1);

namespace MaliBoot\Auth\Provider;

use MaliBoot\Contract\Auth\Authenticatable;

/**
 * Class RpcUserProvider.
 */
class RpcUserProvider extends AbstractUserProvider
{
    public function retrieveById($identifier): ?Authenticatable
    {
        return call_user_func_array([make($this->config['rpc']), 'retrieveById'], [$identifier]);
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        return call_user_func_array([make($this->config['rpc']), 'retrieveByCredentials'], [$credentials]);
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return $user->getAuthIdentifier() === $credentials['id'];
    }
}
