<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Statistic\Statistic;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClassSubjectRepository")
 */
class ClassSubject
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
    private $idclass;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher", inversedBy="teacher")
     * @ORM\JoinColumn(nullable=false)
     */
    private $teacher;

    /**
     * @ORM\Column(type="text")
     */
    private $idsubject;

    /**
     * @ORM\Column(type="text")
     */
    private $namesubject;

    /**
     * @ORM\Column(type="text")
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numberlesson;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SurveyForm", mappedBy="classSubject")
     */
    private $surveyForm;

    public function __construct()
    {
        $this->surveyForm = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdclass(): ?string
    {
        return $this->idclass;
    }

    public function setIdclass(string $idclass): self
    {
        $this->idclass = $idclass;

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getIdsubject(): ?string
    {
        return $this->idsubject;
    }

    public function setIdsubject(string $idsubject): self
    {
        $this->idsubject = $idsubject;

        return $this;
    }

    public function getNamesubject(): ?string
    {
        return $this->namesubject;
    }

    public function setNamesubject(string $namesubject): self
    {
        $this->namesubject = $namesubject;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getNumberlesson(): ?string
    {
        return $this->numberlesson;
    }

    public function setNumberlesson(string $numberlesson): self
    {
        $this->numberlesson = $numberlesson;

        return $this;
    }

    /**
     * @return Collection|SurveyForm[]
     */
    public function getSurveyForm(): Collection
    {
        return $this->surveyForm;
    }

    public function addSurveyForm(SurveyForm $surveyForm): self
    {
        if (!$this->surveyForm->contains($surveyForm)) {
            $this->surveyForm[] = $surveyForm;
            $surveyForm->addClassSubject($this);
        }

        return $this;
    }

    public function removeSurveyForm(SurveyForm $surveyForm): self
    {
        if ($this->surveyForm->contains($surveyForm)) {
            $this->surveyForm->removeElement($surveyForm);
            $surveyForm->removeClassSubject($this);
        }

        return $this;
    }


    public function toString(): array
    {
        return ['idClass'=>$this->idclass,
            'subjectName'=>$this->namesubject,
            'numberLesson'=>$this->numberlesson,
            'location'=>$this->location];
    }

    /**
     * @param array $appendix
     * @return array
     */
    public function getStatistic(array $appendix): array
    {
        // TO DO calculate 6 properties
        $retData = [];

        $statistic = [];
        foreach ($appendix as $key=>$value) {
            $statistic[$key] = [0, 0];
        }

        foreach ($this->surveyForm as $s) {
            $contentData = $s->getContent();
            if($contentData !== null) {
                foreach ($contentData as $key=>$value) {


                    $statistic[$key][0] += (float) $value;
                    $statistic[$key][1] += 1;
                }
            }

        }

        $sta = new Statistic($statistic);
        $sta->calculate();
        $retData['M'] = $sta->getAverage();
        $retData['STD'] = $sta->getVariant();

        return $retData;
    }

}
