<?php

declare(strict_types=1);

/*
 * This file is part of the Liip/FunctionalTestBundle
 *
 * (c) Lukas Kahwe Smith <smith@pooteeweet.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Liip\Acme\Tests\Test;

use Liip\Acme\Tests\AppConfigLeanFramework\AppConfigLeanFrameworkKernel;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Test Lean Framework - with validator component disabled.
 *
 * Use Tests/AppConfigLeanFramework/AppConfigLeanFrameworkKernel.php instead of
 * Tests/App/AppKernel.php.
 * So it must be loaded in a separate process.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class WebTestCaseConfigLeanFrameworkTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return AppConfigLeanFrameworkKernel::class;
    }

    public function setUp(): void
    {
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            throw new \Exception($errstr);
        });
    }

    public function tearDown(): void
    {
        restore_error_handler();
    }


    public function testAssertStatusCode(): void
    {
        $client = static::makeClient();

        $path = '/';
        $client->request('GET', $path);

        $this->assertStatusCode(200, $client);
    }

    public function testAssertValidationErrorsTriggersError(): void
    {
        $client = static::makeClient();

        $path = '/form';
        $client->request('GET', $path);

        try {
            $this->assertValidationErrors([], $client->getContainer());
        } catch (\Exception $e) {
            $this->assertSame(
                'Method Liip\FunctionalTestBundle\Utils\HttpAssertions::assertValidationErrors() can not be used as the validation component of the Symfony framework is disabled.',
                $e->getMessage()
            );

            return;
        }

        $this->fail('Test failed.');
    }
}
