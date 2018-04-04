<?php
namespace WHAAM\PrivateApplication\Common\Security\Authentication\Provider;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;


class ApiKeyUserProvider implements UserProviderInterface
{
    private $users;

    public function __construct($rootDir)
    {
        $security = Yaml::parse(file_get_contents($rootDir . '/config/security.yml'));

        $this->users = $security['security']['providers']['in_memory']['memory']['users'];
    }

    public function getUsernameForApiKey($apiKey)
    {
        foreach($this->users as $username => $userData) {
            if($userData['password'] == $apiKey) {
                return $username;
            }
        }

        return false;
    }

    public function loadUserByUsername($username)
    {
        return new User(
            $username,
            null,
            array('ROLE_API')
        );
    }

    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return 'Symfony\Component\Security\Core\User\User' === $class;
    }
}