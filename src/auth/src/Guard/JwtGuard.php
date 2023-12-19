<?php

declare(strict_types=1);

namespace MaliBoot\Auth\Guard;

use Hyperf\Context\Context;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Stringable\Str;
use MaliBoot\Auth\Exception\AuthenticationException;
use MaliBoot\Auth\Exception\UnauthorizedException;
use MaliBoot\Auth\Traits\GuardHelpers;
use MaliBoot\Contract\Auth\Authenticatable;
use MaliBoot\Contract\Auth\Guard as GuardContract;
use MaliBoot\Contract\Auth\UserProvider;
use Qbhy\SimpleJwt\Exceptions\TokenExpiredException;
use Qbhy\SimpleJwt\JWTManager;

class JwtGuard implements GuardContract
{
    use GuardHelpers;

    protected JWTManager $jwtManager;

    protected RequestInterface $request;

    protected string $headerName = 'Authorization';

    protected string $name;

    public function __construct(
        array $config,
        string $name,
        UserProvider $provider,
        RequestInterface $request
    ) {
        $this->name = $name;
        $this->provider = $provider;
        $this->headerName = $config['header_name'] ?? 'Authorization';
        $this->jwtManager = new JWTManager($config);
        $this->request = $request;
    }

    public function parseToken()
    {
        $header = $this->request->header($this->headerName, '');
        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }

        if ($this->request->has('token')) {
            return $this->request->input('token');
        }

        return null;
    }

    public function login(Authenticatable $user, array $payload = []): string
    {
        $token = $this->getJwtManager()->make(array_merge($payload, [
            'uid' => $user->getAuthIdentifier(),
            's' => Str::random(),
        ]))->token();

        Context::set($this->resultKey($token), $user);

        return $token;
    }

    /**
     * 获取用于存到 context 的 key.
     *
     * @param mixed $token
     * @return string
     */
    public function resultKey($token)
    {
        return $this->name . '.auth.result' . $this->getJti($token);
    }

    public function user(?string $token = null): ?Authenticatable
    {
        $token = $token ?? $this->parseToken();
        if (Context::has($key = is_string($token) ? $this->resultKey($token) : '_nothing')) {
            $result = Context::get($key);
            if ($result instanceof UnauthorizedException) {
                throw $result;
            }
            return $result ?: null;
        }

        try {
            if ($token) {
                $jwt = $this->getJwtManager()->parse($token);
                $uid = $jwt->getPayload()['uid'] ?? null;
                $user = $uid ? $this->provider->retrieveById($uid) : null;
                Context::set($key, $user ?: 0);

                return $user;
            }

            throw new UnauthorizedException('The token is required.', $this);
        } catch (\Throwable $exception) {
            $newException = $exception instanceof AuthenticationException ? $exception : new UnauthorizedException(
                $exception->getMessage(),
                $this,
                $exception
            );
            Context::set($key, $newException);
            throw $newException;
        }
    }

    public function check(?string $token = null): bool
    {
        try {
            return $this->user($token) instanceof Authenticatable;
        } catch (AuthenticationException $exception) {
            return false;
        }
    }

    public function guest(?string $token = null): bool
    {
        return ! $this->check($token);
    }

    /**
     * 刷新 token，旧 token 会失效.
     */
    public function refresh(?string $token = null): ?string
    {
        $token = $token ?: $this->parseToken();

        if ($token) {
            Context::set($this->resultKey($token), null);

            try {
                $jwt = $this->getJwtManager()->parse($token);
            } catch (TokenExpiredException $exception) {
                $jwt = $exception->getJwt();
            }

            $this->getJwtManager()->addBlacklist($jwt);

            return $this->getJwtManager()->refresh($jwt)->token();
        }

        return null;
    }

    public function logout($token = null): bool
    {
        if ($token = $token ?? $this->parseToken()) {
            Context::set($this->resultKey($token), null);
            $this->getJwtManager()->addBlacklist(
                $this->getJwtManager()->parse($token)
            );
            return true;
        }
        return false;
    }

    public function getPayload($token = null): array|null
    {
        if ($token = $token ?? $this->parseToken()) {
            return $this->getJwtManager()->justParse($token)->getPayload();
        }
        return null;
    }

    public function getJwtManager(): JWTManager
    {
        return $this->jwtManager;
    }

    public function id($token = null): int|string|null
    {
        if ($token = $token ?? $this->parseToken()) {
            return $this->getJwtManager()->parse($token)->getPayload()['uid'];
        }
        return null;
    }

    public function loginUsingId(int|string $id): mixed
    {
        $token = $this->getJwtManager()->make([
            'uid' => $id,
            's' => Str::random(),
        ])->token();

        Context::set($this->resultKey($token), $this->provider->retrieveById($id));

        return $token;
    }

    /**
     * 获取 token 标识.
     * 为了性能，直接 md5.
     *
     * @return mixed|string
     */
    protected function getJti(string $token): string
    {
        return md5($token);
    }
}
