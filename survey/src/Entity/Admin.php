<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminRepository")
 */
class Admin
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="admin", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $userdb;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullname;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserdb(): ?User
    {
        return $this->userdb;
    }

    public function setUserdb(User $userdb): self
    {
        $this->userdb = $userdb;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }
}
