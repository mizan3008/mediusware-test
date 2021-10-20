<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MigrateFreshSeedOnce;

    public function actingAsAuthenticateUser(): void
    {
        $user = User::whereEmail('admin@example.com')->first();
        $this->actingAs($user);
    }
}
