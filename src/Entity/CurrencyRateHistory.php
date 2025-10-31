<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\CurrencyManageBundle\Repository\CurrencyRateHistoryRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;

#[ORM\Entity(repositoryClass: CurrencyRateHistoryRepository::class)]
#[ORM\Table(name: 'starhome_currency_rate_history', options: ['comment' => '货币汇率历史记录'])]
#[ORM\Index(name: 'starhome_currency_rate_history_idx_currency_date', columns: ['currency_code', 'rate_date'])]
class CurrencyRateHistory implements \Stringable
{
    use CreateTimeAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(length: 32, options: ['comment' => '货币代码'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[IndexColumn]
    private string $currencyCode = '';

    #[ORM\Column(length: 32, options: ['comment' => '货币名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    private string $currencyName = '';

    #[ORM\Column(length: 32, options: ['comment' => '货币符号'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    private string $currencySymbol = '';

    #[ORM\Column(length: 8, nullable: true, options: ['comment' => '国旗代码'])]
    #[Assert\Length(max: 8)]
    private ?string $flag = null;

    #[ORM\Column(options: ['comment' => '对人民币汇率'])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 99999)]
    private float $rateToCny = 0.0;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '汇率日期'])]
    #[Assert\NotNull]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    #[IndexColumn]
    private \DateTimeImmutable $rateDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    public function getCurrencyName(): string
    {
        return $this->currencyName;
    }

    public function setCurrencyName(string $currencyName): void
    {
        $this->currencyName = $currencyName;
    }

    public function getCurrencySymbol(): string
    {
        return $this->currencySymbol;
    }

    public function setCurrencySymbol(string $currencySymbol): void
    {
        $this->currencySymbol = $currencySymbol;
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setFlag(?string $flag): void
    {
        $this->flag = $flag;
    }

    public function getRateToCny(): float
    {
        return $this->rateToCny;
    }

    public function setRateToCny(float $rateToCny): void
    {
        $this->rateToCny = $rateToCny;
    }

    public function getRateDate(): \DateTimeImmutable
    {
        return $this->rateDate;
    }

    public function setRateDate(\DateTimeImmutable $rateDate): void
    {
        $this->rateDate = $rateDate;
    }

    public function __toString(): string
    {
        return null !== $this->getId()
            ? "{$this->getCurrencyName()}[{$this->getCurrencySymbol()}] - {$this->getRateDate()->format('Y-m-d')}"
            : '';
    }
}
