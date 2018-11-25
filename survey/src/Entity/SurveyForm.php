<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SurveyFormRepository")
 */
class SurveyForm
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ClassSubject", inversedBy="classSubject")
     */
    private $classSubject;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Student", inversedBy="surveyForms")
     */
    private $student;

    /**
     * @ORM\Column(type="json")
     */
    private $content = [];

    public function __construct()
    {
        $this->classSubject = new ArrayCollection();
        $this->student = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|ClassSubject[]
     */
    public function getClassSubject(): Collection
    {
        return $this->classSubject;
    }

    public function addClassSubject(ClassSubject $classSubject): self
    {
        if (!$this->classSubject->contains($classSubject)) {
            $this->classSubject[] = $classSubject;
        }

        return $this;
    }

    public function removeClassSubject(ClassSubject $classSubject): self
    {
        if ($this->classSubject->contains($classSubject)) {
            $this->classSubject->removeElement($classSubject);
        }

        return $this;
    }

    /**
     * @return Collection|Student[]
     */
    public function getStudent(): Collection
    {
        return $this->student;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->student->contains($student)) {
            $this->student[] = $student;
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->student->contains($student)) {
            $this->student->removeElement($student);
        }

        return $this;
    }

    public function getContent(): ?array
    {
        return $this->content;
    }

    public function setContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getNecessaryInfo($criterialLevels): array
    {
        $retData = [];
        $retContent = $this->content;
        if($this->content === []) {
            foreach ($criterialLevels as $key=>$value) {
                $retContent[$key] = 0;
            }
        }
        $retData = ['content'=>$retContent];
        foreach ($this->classSubject[0]->toString() as $key=>$value) {
            $retData[$key] = $value;
        }

        return $retData;
    }
}
