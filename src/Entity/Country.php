<?php

namespace Tourze\CurrencyManageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tourze\CurrencyManageBundle\Repository\CountryRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\GBT2659\Alpha2Code;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\Table(name: 'starhome_country', options: ["comment" => '国家管理'])]
#[ORM\UniqueConstraint(name: 'uniq_country_code', columns: ['code'])]
class Country implements \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[IndexColumn]
    #[ORM\Column(length: 2, options: ['comment' => '国家代码（ISO 3166-1 alpha-2）'])]
    private string $code = '';

    #[ORM\Column(length: 64, options: ['comment' => '国家名称'])]
    private string $name = '';

    #[ORM\Column(length: 8, nullable: true, options: ['comment' => '国旗代码'])]
    private ?string $flagCode = null;

    #[ORM\Column(options: ['comment' => '是否有效', 'default' => true])]
    private bool $valid = true;


    #[ORM\OneToMany(targetEntity: Currency::class, mappedBy: 'country', fetch: 'EXTRA_LAZY')]
    private Collection $currencies;

    public function __construct()
    {
        $this->currencies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFlagCode(): ?string
    {
        return $this->flagCode;
    }

    public function setFlagCode(?string $flagCode): static
    {
        $this->flagCode = $flagCode;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): static
    {
        $this->valid = $valid;

        return $this;
    }


    /**
     * @return Collection<int, Currency>
     */
    public function getCurrencies(): Collection
    {
        return $this->currencies;
    }

    public function addCurrency(Currency $currency): static
    {
        if (!$this->currencies->contains($currency)) {
            $this->currencies->add($currency);
            $currency->setCountry($this);
        }

        return $this;
    }

    public function removeCurrency(Currency $currency): static
    {
        if ($this->currencies->removeElement($currency)) {
            // set the owning side to null (unless already changed)
            if ($currency->getCountry() === $this) {
                $currency->setCountry(null);
            }
        }

        return $this;
    }

    /**
     * 从 Alpha2Code 枚举创建 Country 实例
     */
    public static function fromAlpha2Code(Alpha2Code $alpha2Code): self
    {
        $country = new self();
        $country->setCode($alpha2Code->value);
        $country->setName($alpha2Code->getLabel());
        $country->setFlagCode(strtolower($alpha2Code->value));

        return $country;
    }

    /**
     * 获取对应的 Alpha2Code 枚举
     */
    public function getAlpha2Code(): ?Alpha2Code
    {
        return Alpha2Code::tryFrom($this->code);
    }

    public function __toString(): string
    {
        return null !== $this->getId() ? "{$this->getName()}[{$this->getCode()}]" : '';
    }
} 