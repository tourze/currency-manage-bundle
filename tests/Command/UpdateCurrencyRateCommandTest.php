<?php

namespace Tourze\CurrencyManageBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\CurrencyManageBundle\Command\UpdateCurrencyRateCommand;
use Tourze\CurrencyManageBundle\Service\CurrencyRateService;

class UpdateCurrencyRateCommandTest extends TestCase
{
    private UpdateCurrencyRateCommand $command;
    private CurrencyRateService&MockObject $currencyRateService;
    private InputInterface&MockObject $input;
    private OutputInterface&MockObject $output;

    protected function setUp(): void
    {
        $this->currencyRateService = $this->createMock(CurrencyRateService::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        
        $this->command = new UpdateCurrencyRateCommand($this->currencyRateService);
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

    public function test_execute_successfulSync(): void
    {
        $syncResult = [
            'updatedCount' => 5,
            'historyCount' => 3,
            'updateTime' => new \DateTime(),
        ];

        $this->currencyRateService->expects($this->once())
            ->method('syncRates')
            ->willReturn($syncResult);

        // 使用回调函数验证输出
        $outputMessages = [];
        $this->output->expects($this->exactly(2))
            ->method('writeln')
            ->willReturnCallback(function ($message) use (&$outputMessages) {
                $outputMessages[] = $message;
            });

        $result = $this->executeCommand();

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertCount(2, $outputMessages);
        $this->assertSame("成功更新了 5 个货币的汇率信息", $outputMessages[0]);
        $this->assertSame("成功记录了 3 条新的历史汇率数据", $outputMessages[1]);
    }

    public function test_execute_withException(): void
    {
        $exception = new \Exception('API调用失败');

        $this->currencyRateService->expects($this->once())
            ->method('syncRates')
            ->willThrowException($exception);

        $this->output->expects($this->once())
            ->method('writeln')
            ->with('<error>汇率同步失败：API调用失败</error>');

        $result = $this->executeCommand();

        $this->assertSame(Command::FAILURE, $result);
    }

    public function test_execute_withZeroUpdates(): void
    {
        $syncResult = [
            'updatedCount' => 0,
            'historyCount' => 0,
            'updateTime' => new \DateTime(),
        ];

        $this->currencyRateService->expects($this->once())
            ->method('syncRates')
            ->willReturn($syncResult);

        // 使用回调函数验证输出
        $outputMessages = [];
        $this->output->expects($this->exactly(2))
            ->method('writeln')
            ->willReturnCallback(function ($message) use (&$outputMessages) {
                $outputMessages[] = $message;
            });

        $result = $this->executeCommand();

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertCount(2, $outputMessages);
        $this->assertSame("成功更新了 0 个货币的汇率信息", $outputMessages[0]);
        $this->assertSame("成功记录了 0 条新的历史汇率数据", $outputMessages[1]);
    }

    public function test_constructor_requiresCurrencyRateService(): void
    {
        $reflection = new \ReflectionClass(UpdateCurrencyRateCommand::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertSame('currencyRateService', $parameters[0]->getName());
    }
} 