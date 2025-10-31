<?php

namespace Tourze\CurrencyManageBundle\Tests\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'biz_role', options: ['comment' => '业务角色表'])]
class BizRole implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '角色名称'])]
    #[Assert\Length(max: 255, maxMessage: '角色名称不能超过{{ limit }}个字符')]
    #[Assert\NotBlank(message: '角色名称不能为空')]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '角色代码'])]
    #[Assert\Length(max: 255, maxMessage: '角色代码不能超过{{ limit }}个字符')]
    #[Assert\NotBlank(message: '角色代码不能为空')]
    private ?string $code = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function __toString(): string
    {
        return $this->name ?? $this->code ?? (string) ($this->id ?? '');
    }
}
