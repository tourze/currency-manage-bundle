<?php

namespace Tourze\CurrencyManageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\CurrencyManageBundle\Repository\CurrencyRateHistoryRepository;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;

#[ORM\Entity(repositoryClass: CurrencyRateHistoryRepository::class)]
#[ORM\Table(name: 'starhome_currency_rate_history', options: ["comment" => '货币汇率历史记录'])]
#[ORM\Index(name: 'idx_currency_code', columns: ['currency_code'])]
#[ORM\Index(name: 'idx_rate_date', columns: ['rate_date'])]
#[ORM\Index(name: 'idx_currency_date', columns: ['currency_code', 'rate_date'])]
class CurrencyRateHistory implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(length: 32, options: ['comment' => '货币代码'])]
    private string $currencyCode = '';

    #[ORM\Column(length: 32, options: ['comment' => '货币名称'])]
    private string $currencyName = '';

    #[ORM\Column(length: 32, options: ['comment' => '货币符号'])]
    private string $currencySymbol = '';

    #[ORM\Column(length: 8, nullable: true, options: ['comment' => '国旗代码'])]
    private ?string $flag = null;

    #[ORM\Column(options: ['comment' => '对人民币汇率'])]
    private float $rateToCny = 0.0;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '汇率日期'])]
    private \DateTimeImmutable $rateDate;

    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '记录创建时间'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): static
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function getCurrencyName(): string
    {
        return $this->currencyName;
    }

    public function setCurrencyName(string $currencyName): static
    {
        $this->currencyName = $currencyName;

        return $this;
    }

    public function getCurrencySymbol(): string
    {
        return $this->currencySymbol;
    }

    public function setCurrencySymbol(string $currencySymbol): static
    {
        $this->currencySymbol = $currencySymbol;

        return $this;
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setFlag(?string $flag): static
    {
        $this->flag = $flag;

        return $this;
    }

    public function getRateToCny(): float
    {
        return $this->rateToCny;
    }

    public function setRateToCny(float $rateToCny): static
    {
        $this->rateToCny = $rateToCny;

        return $this;
    }

    public function getRateDate(): \DateTimeImmutable
    {
        return $this->rateDate;
    }

    public function setRateDate(\DateTimeImmutable $rateDate): static
    {
        $this->rateDate = $rateDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function __toString(): string
    {
        return null !== $this->getId() 
            ? "{$this->getCurrencyName()}[{$this->getCurrencySymbol()}] - {$this->getRateDate()->format('Y-m-d')}" 
            : '';
    }
}
