<?php

namespace App\Entity;

use App\Statistic\Statistic;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

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

    public function deleteSurveyForm(EntityManagerInterface $entityManager) {
        foreach ($this->surveyForm as $sF) {
            $entityManager->remove($sF);
        }
    }


    public function toString(): array
    {
        return ['idClass' => $this->idclass,
            'subjectName' => $this->namesubject,
            'numberLesson' => $this->numberlesson,
            'location' => $this->location];
    }

    /**
     * @param array $appendix
     * @return array
     */
    public function getStatistic(array $appendix): array
    {
        $retData = [];

        $retStatistic = $this->getRawStatistic($appendix);
        $statistic = $retStatistic['statistic'];
        $retData['numberStudentDone'] = $retStatistic['numberStudentDone'];
        $retData['statistic'] = [];
        $a = null;
        foreach ($statistic as $key => $value) {
            $data = [];
            foreach ($value as $k => $v) {
                $data[] = [(int) $k, (int) $v];
            }
            $sta = new Statistic($data);
            try {
                $sta->calculate();
                $retData['statistic'][$key]['M'] = round($sta->getAverage(), 2);
                $retData['statistic'][$key]['STD'] = round($sta->getVariant(), 2);

            } catch (\ErrorException $e) {
                $retData['statistic'][$key]['M'] = 0;
                $retData['statistic'][$key]['STD'] = 0;
            }
        }

        return $retData;
    }

    public function getRawStatistic(array $appendix): array
    {
        $retStatistic = [];
        $statistic = [];

        $retStatistic['numberStudentDone'] = 0;
        foreach ($appendix as $key => $value) {
            $statistic[$key] = [];
            for($i = 1;$i <= 5; ++$i){
                $statistic[$key][$i] =0;
            }

        }

        foreach ($this->surveyForm as $s) {
            $contentData = $s->getContent();
            if ($contentData !== null && count($contentData) >= 1) {
                $retStatistic['numberStudentDone'] += 1;
                foreach ($contentData as $key => $value) {
                    if(array_key_exists($key, $statistic)) {
                        $statistic[$key][(int)$value] += 1;
                    }
                }
            }

        }
        $retStatistic['statistic'] = $statistic;
        return $retStatistic;
    }

    public function getFullInfo(): array
    {
        $doneStudent = 0;
        foreach ($this->surveyForm as $s) {
            $contentData = $s->getContent();
            if ($contentData !== null && count($contentData) >= 1) {
                $doneStudent += 1;
            }

        }
        return ['subjectName' => $this->namesubject,
            'idClass' => $this->idclass,
            'teacher' => $this->getTeacher()->getFullname(),
            'Sỹ số' =>count($this->surveyForm),
            'Serveyed'=>$doneStudent];
    }
}
