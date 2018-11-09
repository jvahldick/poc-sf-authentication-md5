<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, EncoderAwareInterface
{
    private $id;
    private $username;
    private $password;
    private $roles;
    private $active;
    private $legacyUser;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->active = true;
        $this->legacyUser = true;
        $this->roles = [];
    }

    public function getId(): ?string
    {
        return (string)$this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function addRole(string $role): self
    {
        $roles = $this->roles;
        $roles[] = $role;

        $this->roles = array_unique($roles);

        return $this;
    }

    public function removeRole(string $role): void
    {
        if (false === $key = array_search($role, $this->roles)) {
            return;
        }

        unset($this->roles[$key]);
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function inactivate(): void
    {
        $this->active = false;
    }

    public function isLegacyUser(): ?bool
    {
        return $this->legacyUser;
    }

    public function setLegacyUser(bool $legacyUser): void
    {
        $this->legacyUser = $legacyUser;
    }

    public function getEncoderName()
    {
        if (true === $this->isLegacyUser()) {
            return 'legacy_user_encoder';
        }

        return 'user_encoder';
    }
}
