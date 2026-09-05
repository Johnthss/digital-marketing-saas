<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_phpunit_works(): void
    {
        $this->assertTrue(true);
    }

    public function test_user_class_exists(): void
    {
        $this->assertTrue(class_exists(\App\Models\User::class));
    }

    public function test_agency_class_exists(): void
    {
        $this->assertTrue(class_exists(\App\Models\Agency::class));
    }

    public function test_user_has_role_attribute(): void
    {
        $user = new \App\Models\User(['role' => 'owner']);
        $this->assertEquals('owner', $user->role);
    }

    public function test_user_is_owner_returns_true_for_owner(): void
    {
        $user = new \App\Models\User(['role' => 'owner']);
        $this->assertTrue($user->isOwner());
    }

    public function test_user_is_admin_returns_true_for_admin(): void
    {
        $user = new \App\Models\User(['role' => 'admin']);
        $this->assertTrue($user->isAdmin());
    }

    public function test_user_is_editor_returns_true_for_manager(): void
    {
        $user = new \App\Models\User(['role' => 'manager']);
        $this->assertTrue($user->isEditor());
    }

    public function test_user_is_editor_returns_false_for_member(): void
    {
        $user = new \App\Models\User(['role' => 'member']);
        $this->assertFalse($user->isEditor());
    }

    public function test_agency_has_is_active_attribute(): void
    {
        $agency = new \App\Models\Agency([
            'status' => 'active',
            'subscription_status' => 'active',
        ]);
        $this->assertTrue($agency->isActive);
    }

    public function test_agency_is_not_active_when_cancelled(): void
    {
        $agency = new \App\Models\Agency([
            'status' => 'cancelled',
            'subscription_status' => 'cancelled',
        ]);
        $this->assertFalse($agency->isActive);
    }

    public function test_enterprise_agency_has_all_features(): void
    {
        $agency = new \App\Models\Agency(['subscription_plan' => 'enterprise']);
        $this->assertTrue($agency->isFeatureAvailable('workflow_engine'));
    }
}
