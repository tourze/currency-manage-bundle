<?php

namespace Tourze\CurrencyManageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\CurrencyManageBundle\Repository\CurrencyRepository;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission('货币管理')]
#[Creatable]
#[Editable]
#[Deletable]
#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
#[ORM\Table(name: 'starhome_currency', options:["comment" => '货币管理'])]
class Currency implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Keyword]
    #[ListColumn]
    #[FormField]
    #[ORM\Column(name: 'flags', length: 32, options: ['comment' => '货币标识'])]
    private ?string $symbol = null;

    #[Keyword]
    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 32, options: ['comment' => '货币名称'])]
    private ?string $name = '';

    #[Keyword]
    #[ListColumn]
    #[FormField]
    #[ORM\Column(length: 32, options: ['comment' => '货币代码'])]
    private ?string $code = '';

    #[ListColumn(sorter: true)]
    #[FormField]
    #[ORM\Column(name: 'rateToCny', nullable: true, options: ['comment' => '对人民币汇率'])]
    private ?float $rateToCny = null;

    #[ListColumn]
    #[CreateTimeColumn]
    #[UpdateTimeColumn]
    #[ORM\Column(name: 'rateUpdateDate', type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '汇率更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getRateToCny(): ?float
    {
        return $this->rateToCny;
    }

    public function setRateToCny(?float $rateToCny): static
    {
        $this->rateToCny = $rateToCny;

        return $this;
    }

    public function setUpdateTime(?\DateTimeInterface $createdAt): self
    {
        $this->updateTime = $createdAt;

        return $this;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function __toString(): string
    {
        return $this->getId() ? "{$this->getName()}[{$this->getSymbol()}]" : '';
    }
}
