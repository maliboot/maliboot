<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Dto\Contract\ContextInterface;
use MaliBoot\Utils\Traits\SetPropertiesTrait;

class UserContext implements ContextInterface
{
    use SetPropertiesTrait;

    private int|string $id;

    private string $username;

    private string $nickname;

    /**
     * @var array<string, mixed>
     */
    private array $extends = [];

    public function getId(): int|string
    {
        return $this->id;
    }

    public function setId(int|string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;
        return $this;
    }

    public function getExtends(): array
    {
        return $this->extends;
    }

    public function setExtends(array $extends): static
    {
        $this->extends = $extends;
        return $this;
    }

    /**
     * 获取属性.
     */
    protected function getProperties(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => 'int',
            ],
            'username' => [
                'name' => 'username',
                'type' => 'string',
            ],
            'nickname' => [
                'name' => 'nickname',
                'type' => 'string',
            ],
            'extends' => [
                'name' => 'extends',
                'type' => 'array',
            ],
        ];
    }
}
