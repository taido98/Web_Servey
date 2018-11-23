<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentRepository")
 */
class Student
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
    private $idstudent;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="student", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $iduserdb;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $vnuemail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $course;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SurveyForm", mappedBy="student")
     */
    private $surveyForms;

    public function __construct()
    {
        $this->surveyForms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdstudent(): ?string
    {
        return $this->idstudent;
    }

    public function setIdstudent(string $idstudent): self
    {
        $this->idstudent = $idstudent;

        return $this;
    }

    public function getIduserdb(): ?User
    {
        return $this->iduserdb;
    }

    public function setIduserdb(User $iduserdb): self
    {
        $this->iduserdb = $iduserdb;

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

    public function getCourse(): ?string
    {
        return $this->course;
    }

    public function setCourse(string $course): self
    {
        $this->course = $course;

        return $this;
    }

    /**
     * @return Collection|SurveyForm[]
     */
    public function getSurveyForms(): Collection
    {
        return $this->surveyForms;
    }

    public function addSurveyForm(SurveyForm $surveyForm): self
    {
        if (!$this->surveyForms->contains($surveyForm)) {
            $this->surveyForms[] = $surveyForm;
            $surveyForm->addStudent($this);
        }

        return $this;
    }

    public function removeSurveyForm(SurveyForm $surveyForm): self
    {
        if ($this->surveyForms->contains($surveyForm)) {
            $this->surveyForms->removeElement($surveyForm);
            $surveyForm->removeStudent($this);
        }

        return $this;
    }

    public function getProfile():array
    {
        return ['fullName'=>$this->fullname,
            'idStudent'=>$this->idstudent,
            'courses'=>$this->course];
    }

    public function getNecessarySurveyFormsInfo($criterialLevels): array
    {
        $returnData = [];
        foreach($this->surveyForms as $surveyForm) {
            $returnData[] = $surveyForm->getNecessaryInfo($criterialLevels);
        }
        return $returnData;
    }
}
