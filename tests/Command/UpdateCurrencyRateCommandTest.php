<?php

namespace Tourze\CurrencyManageBundle\Tests\Command;

use Carbon\Carbon;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\CurrencyManageBundle\Command\UpdateCurrencyRateCommand;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\CurrencyManageBundle\Repository\CurrencyRepository;

class UpdateCurrencyRateCommandTest extends TestCase
{
    private UpdateCurrencyRateCommand $command;
    private CurrencyRepository&MockObject $repository;
    private HttpClientInterface&MockObject $httpClient;
    private InputInterface&MockObject $input;
    private OutputInterface&MockObject $output;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(CurrencyRepository::class);
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        
        $this->command = new UpdateCurrencyRateCommand(
            $this->repository,
            $this->httpClient
        );
    }

    public function test_instantiation_createsCommand(): void
    {
        $this->assertInstanceOf(UpdateCurrencyRateCommand::class, $this->command);
    }

    public function test_inheritance_extendsCommand(): void
    {
        $this->assertInstanceOf(Command::class, $this->command);
    }

    private function executeCommand(): int
    {
        $reflection = new \ReflectionClass($this->command);
        $executeMethod = $reflection->getMethod('execute');
        $executeMethod->setAccessible(true);
        
        return $executeMethod->invoke($this->command, $this->input, $this->output);
    }

    public function test_execute_successfulUpdate(): void
    {
        $currency = new Currency();
        $currency->setCode('USD');
        
        $apiResponse = [
            'rates' => [
                'USD' => 7.0,
            ],
            'time_last_updated' => 1640995200, // 2022-01-01 00:00:00
        ];
        
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')
            ->willReturn(json_encode($apiResponse));
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.exchangerate-api.com/v4/latest/CNY')
            ->willReturn($response);
        
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$currency]);
        
        $this->repository->expects($this->once())
            ->method('save')
            ->with($currency);
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
        $this->assertSame(7.0, $currency->getRateToCny());
        $this->assertInstanceOf(\DateTimeInterface::class, $currency->getUpdateTime());
    }

    public function test_execute_noCurrenciesFound(): void
    {
        $apiResponse = [
            'rates' => [
                'USD' => 7.0,
            ],
            'time_last_updated' => 1640995200,
        ];
        
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')
            ->willReturn(json_encode($apiResponse));
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.exchangerate-api.com/v4/latest/CNY')
            ->willReturn($response);
        
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);
        
        $this->repository->expects($this->never())
            ->method('save');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_execute_currencyCodeNotInApiResponse(): void
    {
        $currency = new Currency();
        $currency->setCode('XYZ');
        
        $apiResponse = [
            'rates' => [
                'USD' => 7.0,
                'EUR' => 8.0,
            ],
            'time_last_updated' => 1640995200,
        ];
        
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')
            ->willReturn(json_encode($apiResponse));
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.exchangerate-api.com/v4/latest/CNY')
            ->willReturn($response);
        
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$currency]);
        
        $this->repository->expects($this->never())
            ->method('save');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
        $this->assertNull($currency->getRateToCny());
    }

    public function test_execute_multipleCurrencies(): void
    {
        $currency1 = new Currency();
        $currency1->setCode('USD');
        
        $currency2 = new Currency();
        $currency2->setCode('EUR');
        
        $currency3 = new Currency();
        $currency3->setCode('XYZ'); // 不在API响应中
        
        $apiResponse = [
            'rates' => [
                'USD' => 7.0,
                'EUR' => 8.0,
            ],
            'time_last_updated' => 1640995200,
        ];
        
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')
            ->willReturn(json_encode($apiResponse));
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.exchangerate-api.com/v4/latest/CNY')
            ->willReturn($response);
        
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$currency1, $currency2, $currency3]);
        
        $this->repository->expects($this->exactly(2))
            ->method('save');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
        $this->assertSame(7.0, $currency1->getRateToCny());
        $this->assertSame(8.0, $currency2->getRateToCny());
        $this->assertNull($currency3->getRateToCny());
    }

    public function test_execute_jsonWithZeroRates(): void
    {
        $currency = new Currency();
        $currency->setCode('USD');
        
        $apiResponse = [
            'rates' => [
                'USD' => 0,
            ],
            'time_last_updated' => 1640995200,
        ];
        
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')
            ->willReturn(json_encode($apiResponse));
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn($response);
        
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$currency]);
        
        $this->repository->expects($this->once())
            ->method('save');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
        $this->assertSame(0.0, $currency->getRateToCny());
    }

    public function test_execute_jsonWithNegativeRates(): void
    {
        $currency = new Currency();
        $currency->setCode('USD');
        
        $apiResponse = [
            'rates' => [
                'USD' => -1.5,
            ],
            'time_last_updated' => 1640995200,
        ];
        
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')
            ->willReturn(json_encode($apiResponse));
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn($response);
        
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$currency]);
        
        $this->repository->expects($this->once())
            ->method('save');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
        $this->assertSame(-1.5, $currency->getRateToCny());
    }

    public function test_execute_timestampConversion(): void
    {
        $currency = new Currency();
        $currency->setCode('USD');
        
        $timestamp = 1640995200; // 2022-01-01 00:00:00 UTC
        
        $apiResponse = [
            'rates' => [
                'USD' => 7.0,
            ],
            'time_last_updated' => $timestamp,
        ];
        
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')
            ->willReturn(json_encode($apiResponse));
        
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn($response);
        
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$currency]);
        
        $this->repository->expects($this->once())
            ->method('save');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
        
        $expectedTime = Carbon::createFromTimestamp($timestamp, date_default_timezone_get());
        $this->assertEquals($expectedTime, $currency->getUpdateTime());
    }
} 