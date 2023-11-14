<?php

namespace Boot\System\Core\Abstracts\Tests\PhpUnit;

use Boot\System\Core\Traits\HashIdTrait;
use Boot\System\Core\Traits\TestCaseTrait;
use Boot\System\Core\Traits\TestsTraits\PhpUnit\TestsAuthHelperTrait;
use Boot\System\Core\Traits\TestsTraits\PhpUnit\TestsMockHelperTrait;
use Boot\System\Core\Traits\TestsTraits\PhpUnit\TestsRequestHelperTrait;
use Boot\System\Core\Traits\TestsTraits\PhpUnit\TestsResponseHelperTrait;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;

abstract class TestCase extends LaravelTestCase
{
    use TestCaseTrait,
        TestsRequestHelperTrait,
        TestsResponseHelperTrait,
        TestsMockHelperTrait,
        TestsAuthHelperTrait,
        HashIdTrait,
        LazilyRefreshDatabase;

    /**
     * The base URL to use while testing the application.
     */
    protected string $baseUrl;

    /**
     * Setup the test environment, before each test.
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Reset the test environment, after each test.
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Refresh the in-memory database.
     * Overridden refreshTestDatabase Trait
     */
    protected function refreshInMemoryDatabase(): void
    {
        // Migrate the database
        $this->migrateDatabase();

        // Seed the database
        $this->seed();

        // Install Passport Client for Testing
        $this->setupPassportOAuth2();

        $this->app[Kernel::class]->setArtisan(null);
    }

    /**
     * Refresh a conventional test database.
     * Overridden refreshTestDatabase Trait
     */
    protected function refreshTestDatabase(): void
    {
        if (!RefreshDatabaseState::$migrated) {
            $this->artisan('migrate:fresh');
            $this->seed();
            $this->setupPassportOAuth2();

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }
}
