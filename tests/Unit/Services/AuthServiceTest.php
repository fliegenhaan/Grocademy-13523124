<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    #[Test]
    public function user_can_be_registered_successfully(): void
    {
        // Arrange
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john.doe@test.com',
            'password' => 'Password123',
        ];

        // Act
        $user = $this->authService->registerUser($userData);

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@test.com',
            'username' => 'johndoe',
        ]);
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_can_login_with_correct_email_and_password(): void
    {
        // Arrange
        $user = User::create([
            'first_name' => 'Jane', 'last_name' => 'Doe', 'username' => 'janedoe',
            'email' => 'jane.doe@test.com',
            'password' => Hash::make('Password123'),
        ]);

        $credentials = ['identifier' => 'jane.doe@test.com', 'password' => 'Password123'];
        
        $request = new Request();
        $request->setLaravelSession(app('session')->driver());

        // Act
        $result = $this->authService->loginUser($credentials, $request);

        // Assert
        $this->assertTrue($result);
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function user_can_login_with_correct_username_and_password(): void
    {
        // Arrange
        $user = User::create([
            'first_name' => 'Jane', 'last_name' => 'Doe', 'username' => 'janedoe',
            'email' => 'jane.doe@test.com',
            'password' => Hash::make('Password123'),
        ]);

        $credentials = ['identifier' => 'janedoe', 'password' => 'Password123'];
        
        $request = new Request();
        $request->setLaravelSession(app('session')->driver());

        // Act
        $result = $this->authService->loginUser($credentials, $request);

        // Assert
        $this->assertTrue($result);
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function login_fails_with_incorrect_password(): void
    {
        // Arrange
        User::create([
            'email' => 'jane.doe@test.com',
            'password' => Hash::make('Password123'),
            'first_name' => 'Jane', 'last_name' => 'Doe', 'username' => 'janedoe',
        ]);
        $credentials = ['identifier' => 'jane.doe@test.com', 'password' => 'WrongPassword'];
        $request = new Request();

        // Act
        $result = $this->authService->loginUser($credentials, $request);

        // Assert
        $this->assertFalse($result);
        $this->assertGuest();
    }

    #[Test]
    public function user_can_be_logged_out(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);
        $request = new Request();
        $request->setLaravelSession(app('session')->driver());

        // Act
        $this->authService->logoutUser($request);

        // Assert
        $this->assertGuest();
    }
}