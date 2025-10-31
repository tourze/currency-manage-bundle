<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\GBT2659\Alpha2Code;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Country>
 */
#[AsRepository(entityClass: Country::class)]
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * 根据国家代码查找国家
     */
    public function findByCode(string $code): ?Country
    {
        return $this->findOneBy(['code' => $code]);
    }

    /**
     * 根据 Alpha2Code 枚举查找国家
     */
    public function findByAlpha2Code(Alpha2Code $alpha2Code): ?Country
    {
        return $this->findByCode($alpha2Code->value);
    }

    /**
     * 获取所有有效的国家
     *
     * @return Country[]
     */
    public function findAllValid(): array
    {
        return $this->findBy(['valid' => true], ['name' => 'ASC']);
    }

    /**
     * 根据名称搜索国家
     *
     * @return list<Country>
     */
    public function searchByName(string $name): array
    {
        /** @var list<Country> */
        return $this->createQueryBuilder('c')
            ->where('c.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 获取有货币的国家
     *
     * @return list<Country>
     */
    public function findCountriesWithCurrencies(): array
    {
        /** @var list<Country> */
        return $this->createQueryBuilder('c')
            ->innerJoin('c.currencies', 'cur')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 保存实体
     */
    public function save(Country $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除实体
     */
    public function remove(Country $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 刷新所有待处理的更改
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
