<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

trait MigrateFreshSeedOnce
{
    /**
     * If true, setup has run at least once.
     * @var boolean
     */

    protected static $setUpHasRunOnce = false;

    /**
     * After the first run of setUp "migrate:fresh" and "generate:basic-role-and-permission"
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (!static::$setUpHasRunOnce) {

            Artisan::call('migrate:fresh');

            $this->createUser();

            static::$setUpHasRunOnce = true;
        }
    }

    private function createUser(): void
    {
        factory(User::class)->create([
            'email' => 'admin@example.com',
        ]);
    }
}
