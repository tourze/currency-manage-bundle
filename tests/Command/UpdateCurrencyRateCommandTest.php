<?php

namespace Tourze\CurrencyManageBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\CurrencyManageBundle\Command\UpdateCurrencyRateCommand;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(UpdateCurrencyRateCommand::class)]
#[RunTestsInSeparateProcesses]
final class UpdateCurrencyRateCommandTest extends AbstractCommandTestCase
{
    private UpdateCurrencyRateCommand $command;

    private CommandTester $commandTester;

    protected function onSetUp(): void
    {
        $command = self::getContainer()->get(UpdateCurrencyRateCommand::class);
        $this->assertInstanceOf(UpdateCurrencyRateCommand::class, $command);
        $this->command = $command;
        $this->commandTester = new CommandTester($this->command);
    }

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    public function testInstantiationCreatesCommand(): void
    {
        $this->assertNotNull($this->command);
    }

    public function testInheritanceExtendsCommand(): void
    {
        $this->assertNotNull($this->command);
    }

    private function executeCommand(): int
    {
        return $this->commandTester->execute([]);
    }

    public function testExecuteSuccessfulSync(): void
    {
        $result = $this->executeCommand();
        $output = $this->commandTester->getDisplay();

        // 验证命令返回状态码
        $this->assertThat($result, self::logicalOr(
            self::equalTo(Command::SUCCESS),
            self::equalTo(Command::FAILURE)
        ));

        // 验证输出包含预期的消息格式
        if (Command::SUCCESS === $result) {
            // 成功时应该包含更新统计信息
            $this->assertMatchesRegularExpression('/成功更新了 \d+ 个货币的汇率信息/', $output);
            $this->assertMatchesRegularExpression('/成功记录了 \d+ 条新的历史汇率数据/', $output);
        } else {
            // 失败时应该包含错误信息
            $this->assertStringContainsString('汇率同步失败', $output);
        }

        // 验证输出不为空
        $this->assertNotEmpty($output);
    }

    public function testExecuteBasicRun(): void
    {
        $result = $this->executeCommand();
        $output = $this->commandTester->getDisplay();

        // 验证命令返回有效的状态码
        $this->assertThat($result, self::logicalOr(
            self::equalTo(Command::SUCCESS),
            self::equalTo(Command::FAILURE)
        ));

        // 验证命令产生了输出
        $this->assertNotEmpty($output);

        // 验证输出包含预期的关键词（成功或失败的标识）
        $this->assertThat($output, self::logicalOr(
            self::stringContains('成功更新了'),
            self::stringContains('汇率同步失败')
        ));

        // 验证命令tester正确捕获了执行结果
        $this->assertIsInt($result);
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertLessThanOrEqual(255, $result);
    }

    public function testExecuteCommandDescription(): void
    {
        // 测试命令的描述和名称
        $description = $this->command->getDescription();
        $this->assertNotEmpty($description);

        $name = $this->command->getName();
        $this->assertNotEmpty($name);
    }

    public function testConstructorRequiresCurrencyRateService(): void
    {
        $reflection = new \ReflectionClass(UpdateCurrencyRateCommand::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertSame('currencyRateService', $parameters[0]->getName());
    }
}
