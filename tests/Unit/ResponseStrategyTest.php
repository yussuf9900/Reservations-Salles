<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Strategy\HtmlResponseStrategy;
use App\Http\Strategy\JsonResponseStrategy;
use App\Http\Strategy\ResponseStrategyInterface;
use App\Repository\UserRepositoryInterface;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

final class ResponseStrategyTest extends TestCase
{
    public function testContainerSelectsOnlyConfiguredFormat(): void
    {
        $original = $_ENV['APP_RESPONSE_FORMAT'] ?? null;
        try {
            foreach (['html' => HtmlResponseStrategy::class, 'json' => JsonResponseStrategy::class] as $format => $expected) {
                $_ENV['APP_RESPONSE_FORMAT'] = $format;
                $builder = new ContainerBuilder();
                $builder->addDefinitions(dirname(__DIR__, 2) . '/config/container.php');
                $container = $builder->build();
                $container->set(UserRepositoryInterface::class, $this->createMock(UserRepositoryInterface::class));
                $strategy = $container->get(ResponseStrategyInterface::class);
                $this->assertInstanceOf($expected, $strategy);
                $_ENV['APP_RESPONSE_FORMAT'] = 'invalid';
                $this->assertSame($strategy, $container->get(ResponseStrategyInterface::class));
            }
        } finally {
            if ($original === null) {
                unset($_ENV['APP_RESPONSE_FORMAT']);
            } else {
                $_ENV['APP_RESPONSE_FORMAT'] = $original;
            }
        }
    }

    public function testInvalidConfigurationFailsExplicitly(): void
    {
        $original = $_ENV['APP_RESPONSE_FORMAT'] ?? null;
        $_ENV['APP_RESPONSE_FORMAT'] = 'xml';
        try {
            $builder = new ContainerBuilder();
            $builder->addDefinitions(dirname(__DIR__, 2) . '/config/container.php');
            $this->expectException(\InvalidArgumentException::class);
            $builder->build()->get(ResponseStrategyInterface::class);
        } finally {
            if ($original === null) {
                unset($_ENV['APP_RESPONSE_FORMAT']);
            } else {
                $_ENV['APP_RESPONSE_FORMAT'] = $original;
            }
        }
    }
}
