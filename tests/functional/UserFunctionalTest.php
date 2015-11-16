<?php

namespace App\Http\Utilities;


use Tests\Cases\TestCase;


class UserFunctionalTest extends TestCase {

    /**
     * @test
     */
    public function it_can_login()
    {

        $user = factory(\App\User::class)->create([
            'first_name' => 'Test',
            'last_name'  => 'User',
            'username'   => 'testUser',
            'password'   => bcrypt('testpass123'),
        ]);

        $this->visit('/');

        $this->assertResponseOk();
        $this->see('Username');
        $this->see('Password');
        $this->see('Remember Me');

        $this->press('Login');

        $this->see('The username field is required');
        $this->see('The password field is required');

        $this->type('testUser', 'username');
        $this->type('testpass123', 'password');

        $this->press('Login');

        $this->assertResponseOk();
        $this->see('First Name');
        $this->see('Last Name');

    }

    /**
     * @test
     */
    public function it_can_register_a_user()
    {

        $this->visit('/auth/register');

        $this->assertResponseOk();
        $this->press('Register');
        $this->see('The first name field is required');
        $this->see('The last name field is required');
        $this->see('The username field is required');
        $this->see('The email field is required');
        $this->see('The password field is required');

        $this->type('Test', 'first_name');
        $this->type('User', 'last_name');
        $this->type('testUser', 'username');
        $this->type('testUser@testing.com', 'email');
        $this->type('testpass123', 'password');
        $this->type('testpass123', 'password_confirmation');

        $this->press('Register');

        $this->see('First Name:');
        $this->see('Test');
        $this->see('Last Name:');
        $this->see('User');
        $this->see('Email:');
        $this->see('testUser@testing.com');

    }

    /**
     * @test
     */
    public function it_can_reset_a_password()
    {

        $user = factory(\App\User::class)->create([
            'email' => 'test@test.com'
        ]);

        $this->visit('/password/email');
        $this->press('Send Password Reset Link');

        $this->see('The email field is required');

        $this->type('blah@blah.com', 'email');
        $this->press('Send Password Reset Link');
        $this->see("We can't find a user with that e-mail address.");

        $this->type('test@test.com', 'email');
        $this->press('Send Password Reset Link');

        $this->assertResponseOk();
        $this->dontSee("We can't find a user with that e-mail address.");

        // get the token record so that we can go to that page
        $records = \DB::select('select token from password_resets where email = :email', ['email' => 'test@test.com']);
        $token = $records[0]->token;

        $this->visit('/password/reset/' . $token);
        $this->assertResponseOk();

        $this->press('Reset Password');
        $this->see('The email field is required');
        $this->see('The password field is required');

        $this->type('test@test.com', 'email');
        $this->type('testpass123', 'password');
        $this->type('testpass123', 'password_confirmation');

        $this->press('Reset Password');

        $this->assertResponseOk();

        $this->see('First Name:');
        $this->see('Last Name:');
        $this->see('Email:');
        $this->see('test@test.com');
    }

    /**
     * @test
     */
    public function it_redirects_not_logged_in()
    {

        $this->visit('/profile');

        $this->see('Please log-in.');

    }

}
