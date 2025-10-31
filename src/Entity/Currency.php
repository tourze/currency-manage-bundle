<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\CurrencyManageBundle\Repository\CurrencyRepository;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
#[ORM\Table(name: 'currency_currency', options: ['comment' => '货币管理'])]
class Currency implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(name: 'flags', length: 32, nullable: true, options: ['comment' => '货币标识'])]
    #[Assert\Length(max: 32)]
    private ?string $symbol = null;

    #[ORM\Column(length: 32, options: ['comment' => '货币名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    private string $name = '';

    #[ORM\Column(length: 32, options: ['comment' => '货币代码'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    private string $code = '';

    #[ORM\ManyToOne(targetEntity: Country::class, fetch: 'EXTRA_LAZY', inversedBy: 'currencies')]
    #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id', nullable: true)]
    private ?Country $country = null;

    #[ORM\Column(name: 'rateToCny', nullable: true, options: ['comment' => '对人民币汇率'])]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 99999)]
    private ?float $rateToCny = null;

    #[CreateTimeColumn]
    #[UpdateTimeColumn]
    #[ORM\Column(name: 'rateUpdateDate', type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '汇率更新时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $rateUpdateDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(?string $symbol): void
    {
        $this->symbol = $symbol;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getRateToCny(): ?float
    {
        return $this->rateToCny;
    }

    public function setRateToCny(?float $rateToCny): void
    {
        $this->rateToCny = $rateToCny;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    public function getRateUpdateDate(): ?\DateTimeImmutable
    {
        return $this->rateUpdateDate;
    }

    public function setRateUpdateDate(?\DateTimeImmutable $rateUpdateDate): void
    {
        $this->rateUpdateDate = $rateUpdateDate;
    }

    public function __toString(): string
    {
        return null !== $this->getId() ? "{$this->getName()}[{$this->getSymbol()}]" : '';
    }
}
