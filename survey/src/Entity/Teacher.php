<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeacherRepository")
 */
class Teacher
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idteacher;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="teacher", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $userdb;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $vnuemail;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClassSubject", mappedBy="teacher")
     */
    private $teacher;

    public function __construct()
    {
        $this->teacher = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdteacher(): ?string
    {
        return $this->idteacher;
    }

    public function setIdteacher(string $idteacher): self
    {
        $this->idteacher = $idteacher;

        return $this;
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

    public function getVnuemail(): ?string
    {
        return $this->vnuemail;
    }

    public function setVnuemail(string $vnuemail): self
    {
        $this->vnuemail = $vnuemail;

        return $this;
    }

    /**
     * @return Collection|ClassSubject[]
     */
    public function getTeacher(): Collection
    {
        return $this->teacher;
    }

    public function addTeacher(ClassSubject $teacher): self
    {
        if (!$this->teacher->contains($teacher)) {
            $this->teacher[] = $teacher;
            $teacher->setTeacher($this);
        }

        return $this;
    }

    public function removeTeacher(ClassSubject $teacher): self
    {
        if ($this->teacher->contains($teacher)) {
            $this->teacher->removeElement($teacher);
            // set the owning side to null (unless already changed)
            if ($teacher->getTeacher() === $this) {
                $teacher->setTeacher(null);
            }
        }

        return $this;
    }
}
