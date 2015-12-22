<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
namespace Infuse;

use App\Users\Models\User;

class Test implements PHPUnit_Framework_TestListener
{
    /**
     * @var Application
     */
    public static $app;

    /**
     * @var string
     */
    public static $userEmail = 'test@example.com';

    /**
     * @var string
     */
    public static $userPassword = 'testpassword';

    public function __construct()
    {
        $config = @include 'config.php';
        if (!$config) {
            $config = [];
        }

        $config['logger']['enabled'] = false;

        self::$app = new Application($config);

        ini_set('display_errors', 1);

        /* Create a test user and sign in */
        if (class_exists('App\Users\Models\User')) {
            $user = new User();
            $testInfo = [
                'user_email' => self::$userEmail,
                'user_password' => [self::$userPassword, self::$userPassword],
            ];
            if (property_exists($user, 'testUser')) {
                $testInfo = array_replace($testInfo, $user::$testUser);
            } else {
                $testInfo = array_replace($testInfo, [
                    'first_name' => 'Bob',
                    'ip' => '127.0.0.1', ]);
            }

            $existingUser = User::where('user_email', $testInfo['user_email'])
                ->first();
            if ($existingUser) {
                $existingUser->grantAllPermissions()->delete();
            }

            $success = $user->create($testInfo);

            self::$app['user'] = new User($user->id(), true);
        }

        // TODO custom listeners should be used instead
        self::$app['config']->set('email.type', 'nop');
    }

    public function startTest(PHPUnit_Framework_Test $test)
    {
        if (class_exists('App\Users\Models\User')) {
            self::$app['user']->disableSU();
        }
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        printf("\n\n%s:\n", $suite->getName());
    }
}
