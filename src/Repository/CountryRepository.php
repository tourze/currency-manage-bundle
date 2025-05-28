<?php

namespace Tourze\CurrencyManageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\GBT2659\Alpha2Code;

/**
 * @extends ServiceEntityRepository<Country>
 *
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * 保存国家
     */
    public function save(Country $country, bool $flush = false): void
    {
        $this->getEntityManager()->persist($country);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除国家
     */
    public function remove(Country $country, bool $flush = false): void
    {
        $this->getEntityManager()->remove($country);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
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
     */
    public function findAllValid(): array
    {
        return $this->findBy(['valid' => true], ['name' => 'ASC']);
    }

    /**
     * 根据名称搜索国家
     */
    public function searchByName(string $name): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 获取有货币的国家
     */
    public function findCountriesWithCurrencies(): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.currencies', 'cur')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 刷新所有待处理的更改
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
} 