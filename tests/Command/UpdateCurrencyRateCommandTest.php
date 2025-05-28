<?php

namespace Tourze\CurrencyManageBundle\Tests\Command;

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
        $apiResponse = [
            'rates' => [
                'USD' => 7.0,
                'EUR' => 8.0,
                'CNY' => 1.0,
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
        
        // 模拟findByCode返回null，表示需要创建新记录
        $this->repository->method('findByCode')
            ->willReturn(null);
        
        // 期望save方法被调用多次（对应API响应中的货币数量）
        $this->repository->expects($this->atLeastOnce())
            ->method('save')
            ->with($this->isInstanceOf(Currency::class), false);
        
        // 期望flush被调用一次
        $this->repository->expects($this->once())
            ->method('flush');
        
        // 期望输出成功信息
        $this->output->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('成功更新了'));
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_execute_noCurrenciesFound(): void
    {
        $apiResponse = [
            'rates' => [
                'XYZ' => 7.0, // 不存在的货币代码
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
        
        // 不期望save方法被调用
        $this->repository->expects($this->never())
            ->method('save');
        
        // 不期望flush被调用
        $this->repository->expects($this->never())
            ->method('flush');
        
        // 期望输出0个更新
        $this->output->expects($this->once())
            ->method('writeln')
            ->with('成功更新了 0 个货币的汇率信息');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_execute_currencyCodeNotInApiResponse(): void
    {
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
        
        // 模拟findByCode返回null
        $this->repository->method('findByCode')
            ->willReturn(null);
        
        // 期望save方法被调用（对于API中存在的货币）
        $this->repository->expects($this->atLeastOnce())
            ->method('save');
        
        // 期望flush被调用
        $this->repository->expects($this->once())
            ->method('flush');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_execute_multipleCurrencies(): void
    {
        $apiResponse = [
            'rates' => [
                'USD' => 7.0,
                'EUR' => 8.0,
                'CNY' => 1.0,
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
        
        // 模拟findByCode返回null，表示需要创建新记录
        $this->repository->method('findByCode')
            ->willReturn(null);
        
        // 期望save方法被调用多次
        $this->repository->expects($this->atLeastOnce())
            ->method('save');
        
        // 期望flush被调用一次
        $this->repository->expects($this->once())
            ->method('flush');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_execute_jsonWithZeroRates(): void
    {
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
        
        // 模拟findByCode返回null
        $this->repository->method('findByCode')
            ->willReturn(null);
        
        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Currency::class), false);
        
        $this->repository->expects($this->once())
            ->method('flush');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_execute_jsonWithNegativeRates(): void
    {
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
        
        // 模拟findByCode返回null
        $this->repository->method('findByCode')
            ->willReturn(null);
        
        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Currency::class), false);
        
        $this->repository->expects($this->once())
            ->method('flush');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_execute_timestampConversion(): void
    {
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
        
        // 模拟findByCode返回null
        $this->repository->method('findByCode')
            ->willReturn(null);
        
        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Currency::class), false);
        
        $this->repository->expects($this->once())
            ->method('flush');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
    }

    public function test_execute_existingCurrencyUpdate(): void
    {
        $existingCurrency = new Currency();
        $existingCurrency->setCode('USD');
        $existingCurrency->setName('旧美元名称');
        $existingCurrency->setSymbol('$');
        
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
            ->willReturn($response);
        
        // 模拟findByCode返回已存在的货币
        $this->repository->method('findByCode')
            ->with('USD')
            ->willReturn($existingCurrency);
        
        $this->repository->expects($this->once())
            ->method('save')
            ->with($existingCurrency, false);
        
        $this->repository->expects($this->once())
            ->method('flush');
        
        $result = $this->executeCommand();
        
        $this->assertSame(Command::SUCCESS, $result);
        $this->assertSame(7.0, $existingCurrency->getRateToCny());
        $this->assertInstanceOf(\DateTimeInterface::class, $existingCurrency->getUpdateTime());
    }
} 