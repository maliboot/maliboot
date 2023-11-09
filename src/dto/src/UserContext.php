<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Dto\Contract\ContextInterface;
use MaliBoot\Lombok\Annotation\Lombok;

#[Lombok]
class UserContext implements ContextInterface
{
    private int|string $id;

    private string $username;

    private string $nickname;

    /**
     * @var array<string, mixed>
     */
    private array $extends = [];
}
