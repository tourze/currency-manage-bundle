<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;

#[When(env: 'test')]
#[When(env: 'dev')]
class CurrencyRateHistoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $sampleRates = [
            ['USD', 'US Dollar', '$', 'us', 7.25, new \DateTimeImmutable('2024-01-01')],
            ['EUR', 'Euro', '€', 'eu', 7.85, new \DateTimeImmutable('2024-01-01')],
            ['JPY', 'Japanese Yen', '¥', 'jp', 0.049, new \DateTimeImmutable('2024-01-01')],
            ['GBP', 'British Pound', '£', 'gb', 9.15, new \DateTimeImmutable('2024-01-01')],
            ['KRW', 'South Korean Won', '₩', 'kr', 0.0055, new \DateTimeImmutable('2024-01-01')],
            ['USD', 'US Dollar', '$', 'us', 7.28, new \DateTimeImmutable('2024-01-02')],
            ['EUR', 'Euro', '€', 'eu', 7.82, new \DateTimeImmutable('2024-01-02')],
        ];

        foreach ($sampleRates as [$code, $name, $symbol, $flag, $rate, $date]) {
            $history = new CurrencyRateHistory();
            $history->setCurrencyCode($code);
            $history->setCurrencyName($name);
            $history->setCurrencySymbol($symbol);
            $history->setFlag($flag);
            $history->setRateToCny($rate);
            $history->setRateDate($date);

            $manager->persist($history);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CurrencyFixtures::class,
        ];
    }

    public function getOrder(): int
    {
        return 4;
    }
}
