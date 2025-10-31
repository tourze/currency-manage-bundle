<?php

namespace Tourze\CurrencyManageBundle\Tests\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'biz_user', options: ['comment' => '业务用户表'])]
class BizUser implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '邮箱地址'])]
    #[Assert\Email(message: '请输入有效的邮箱地址')]
    #[Assert\Length(max: 255, maxMessage: '邮箱地址不能超过{{ limit }}个字符')]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '密码'])]
    #[Assert\Length(max: 255, maxMessage: '密码不能超过{{ limit }}个字符')]
    private ?string $password = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '用户名'])]
    #[Assert\Length(max: 255, maxMessage: '用户名不能超过{{ limit }}个字符')]
    private ?string $username = null;

    /**
     * @var string[]|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '用户角色'])]
    #[Assert\Count(max: 50, maxMessage: '用户角色数量不能超过{{ limit }}个')]
    private ?array $roles = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string[]|null
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @param  string[]|null  $roles
     */
    public function setRoles(?array $roles): void
    {
        $this->roles = $roles;
    }

    public function __toString(): string
    {
        return $this->username ?? $this->email ?? (string) ($this->id ?? '');
    }
}
