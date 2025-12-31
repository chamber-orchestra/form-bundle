<?php

declare(strict_types=1);

namespace Tests\Integrational\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'test_users')]
class TestUser
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    public ?int $id = null;

    #[ORM\Column(type: 'string')]
    public string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }
}
