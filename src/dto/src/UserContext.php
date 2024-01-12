<?php

declare(strict_types=1);

namespace MaliBoot\Dto;

use MaliBoot\Dto\Contract\ContextInterface;
use MaliBoot\Lombok\Annotation\Lombok;

#[Lombok]
class UserContext implements ContextInterface
{
    private int|string $id;

    private int $tenantId;

    private string $username;

    private string $nickname;

    private string $mobile;

    /**
     * @var array<string, mixed>
     */
    private array $extends = [];

    public function initData(array $data): self
    {
        $this->ofData($data);
        $filterKeys = ['id' => 1, 'tenantId' => 1, 'username' => 1, 'nickname' => 1, 'mobile' => 1, 'extends' => 1];
        $filterExt = array_filter($data, fn ($key) => ! isset($filterKeys[$key]), ARRAY_FILTER_USE_KEY);
        $this->extends = array_merge($filterExt, $this->extends);
        return $this;
    }
}
