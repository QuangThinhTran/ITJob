<?php

namespace Tests\Unit;

use App\Constant;
use App\Mail\RegisterMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->roleAdmin = Role::factory()->create([
            'id' => Constant::ROLE_ADMIN
        ]);

        $this->roleCompany = Role::factory()->create([
            'id' => Constant::ROLE_COMPANY
        ]);

        $this->roleCandidate = Role::factory()->create([
            'id' => Constant::ROLE_CANDIDATE
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id
        ]);

        $this->company = User::factory()->create([
            'role_id' => $this->roleCompany->id
        ]);

        $this->candidate = User::factory()->create([
            'role_id' => $this->roleCandidate->id
        ]);
    }

    public function testRegisterAdmin()
    {
        Mail::fake();
        $admin = [
            'name' => fake()->name(),
            'username' => fake()->name(),
            'email' => fake()->unique()->email,
            'phone' => fake()->unique()->phoneNumber,
            'address' => fake()->address,
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'role_id' => $this->roleAdmin->id
        ];

        $responseAdmin = $this->post('user/user-register', $admin);
        $responseAdmin->assertStatus(302);
        $responseAdmin->assertRedirect('dashboard/admin');
        Mail::assertSent(RegisterMail::class);

        $this->assertDatabaseHas('user', [
            'name' => $admin['name'],
            'username' => $admin['username'],
            'email' => $admin['email'],
            'phone' => $admin['phone'],
            'address' => $admin['address'],
        ]);
    }

    public function testRegisterCompany()
    {
        Mail::fake();

        $company = [
            'name' => fake()->name(),
            'username' => fake()->name(),
            'email' => fake()->unique()->email,
            'phone' => fake()->unique()->phoneNumber,
            'address' => fake()->address,
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'role_id' => $this->roleCompany->id
        ];

        $responseAdmin = $this->post('user/user-register', $company);
        $responseAdmin->assertStatus(302);
        $responseAdmin->assertRedirect('company/index');
        Mail::assertSent(RegisterMail::class);

        $this->assertDatabaseHas('user', [
            'name' => $company['name'],
            'username' => $company['username'],
            'email' => $company['email'],
            'phone' => $company['phone'],
            'address' => $company['address'],
        ]);
    }

    public function testRegisterCandidate()
    {
        $candidate = [
            'name' => fake()->name(),
            'username' => fake()->name(),
            'email' => fake()->unique()->email,
            'phone' => fake()->unique()->phoneNumber,
            'address' => fake()->address,
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'role_id' => $this->roleCandidate->id
        ];

        $responseAdmin = $this->post('user/user-register', $candidate);
        $responseAdmin->assertStatus(302);
        $responseAdmin->assertRedirect('/');

        $this->assertDatabaseHas('user', [
            'name' => $candidate['name'],
            'username' => $candidate['username'],
            'email' => $candidate['email'],
            'phone' => $candidate['phone'],
            'address' => $candidate['address'],
        ]);
    }

    public function testLoginWithAdmin()
    {
        Mail::fake();

        $responseAdmin = $this->post('user/user-login', [
            'username' => $this->admin->username,
            'password' => '12345678',
            'remember_token' => $this->admin->remember_token
        ]);

        $responseAdmin->assertStatus(302);
        $responseAdmin->assertRedirect('dashboard/admin');
    }

    public function testLoginWithCompany()
    {
        $responseCompany = $this->post('user/user-login', [
            'username' => $this->company->username,
            'password' => '12345678',
            'remember_token' => $this->company->remember_token
        ]);

        $responseCompany->assertStatus(302);
        $responseCompany->assertRedirect('/company/index');
    }

    public function testLoginWithCandidate()
    {
        $responseCandidate = $this->post('user/user-login', [
            'username' => $this->candidate->username,
            'password' => '12345678',
            'remember_token' => $this->candidate->remember_token
        ]);

        $responseCandidate->assertStatus(302);
        $responseCandidate->assertRedirect('/');
    }
}
