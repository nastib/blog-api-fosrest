<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private $username;
    private $fullname;
    private $email;
    private $avatarUrl;
    private $profileHtmlUrl;

    public function __construct($username, $fullname, $email, $avatarUrl, $profileHtmlUrl)
    {
        $this->username = $username;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->avatarUrl = $avatarUrl;
        $this->profileHtmlUrl = $profileHtmlUrl;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

    public function getProfileHtmlUrl()
    {
        return $this->profileHtmlUrl;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }
}