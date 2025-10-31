<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CurrencyManageBundle\CurrencyManageBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(CurrencyManageBundle::class)]
#[RunTestsInSeparateProcesses]
final class CurrencyManageBundleTest extends AbstractBundleTestCase
{
}
