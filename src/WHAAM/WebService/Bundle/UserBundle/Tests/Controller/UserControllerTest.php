<?php

namespace WHAAM\WebService\Bundle\UserBundle\Tests\Controller;

use WHAAM\PrivateApplication\Common\Tests\CustomWebTestCase;

class UserControllerTest extends CustomWebTestCase
{
    protected $client;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->container = $kernel->getContainer();

        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        $this->context       = $this->container->get('security.context');
    }

    public function __construct()
    {
        $this->client = static::createClient();
    }

    /**
     * Test login with right parameters
     */
    public function testPostLogin()
    {
        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/login?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'username' => 'test',
                'password' => 'test',
            )
        );

        $response = $this->client->getResponse();

        $result = json_decode($response->getContent());

        $this->assertTrue(array_key_exists('userId', $result));
        $this->assertTrue(array_key_exists('sessionId', $result));

        $this->assertJsonResponse($response, 200);
    }

    /**
     * Test userData with right parameters
     */
    public function testUserData()
    {
        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $crawler = $this->client->request(
            'GET',
            'https://127.0.0.66/api/user-data/' . $user->getMobileAppSessionId() . '/pt_PT?apikey=a3332be7d986075cbd715f3ffc183bc7'
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 200);
    }

    /**
     * Test userData with invalid locale
     */
    public function testInvalidLocaleUserData()
    {
        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $crawler = $this->client->request(
            'GET',
            'https://127.0.0.66/api/user-data/' . $user->getMobileAppSessionId() . '/EL_GT?apikey=a3332be7d986075cbd715f3ffc183bc7'
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400);
    }

    /**
     * Test login with wrong username
     */
    public function testInvalidUserPostLogin()
    {
        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/login?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'username' => 'wrongUser',
                'password' => 'test',
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 404);
    }

    /**
     * Test login with invalid password
     */
    public function testInvalidPasswordPostLogin()
    {
        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/login?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'username' => 'test',
                'password' => 'wrongPassword',
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400);
    }

    /**
     * Test logout with right parameters
     */
    public function testPostLogout()
    {
        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/logout?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'sessionId' => $user->getMobileAppSessionId(),
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 200);
    }

    /**
     * Test logout with wrong sessionId
     */
    public function testInvalidPostLogout()
    {
        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/logout?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'sessionId' => 'test',
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400);
    }
}
