<?php

namespace App\Entity;

use App\Statistic\Statistic;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;

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

    public function getProfile(): array
    {
        return ['fullName' => $this->fullname,
            'idStudent' => $this->idteacher,
            'vnuemail' => $this->vnuemail];
    }

    public function getStatisticAndClassInfo($appendix, $entityManager): array
    {
        $retData = [];


        $M3 = null;
        $STD3 = null;
        // statistic 3 all class same teacher
        $classesSameTeacher = $entityManager->getRepository(ClassSubject::class)->findBy(['teacher' => $this]);
        $statistic3 = [];
        foreach ($appendix as $key => $value) {
            $statistic3[$key] = [0, 0];
        }

        foreach ($classesSameTeacher as $item) {
            $retStatistic = $item->getRawStatistic($appendix);
            $rawStatistic = $retStatistic['statistic'];;
            foreach ($rawStatistic as $key => $value) {
                $statistic3[$key][0] += (float)$value;
                $statistic3[$key][1] += 1;
            }

        }
        $sta = new Statistic($statistic3);
        try {
            $sta->calculate();
            $M3 = $sta->getAverage();
            $STD3 = $sta->getVariant();
        }catch (\ErrorException $e) {
            $M3 = 0;
            $STD3 = 0;
        }


        // statistic 1 each class
        foreach ($this->teacher as $class) {
            $classData = $class->toString();
            $classData['statistic'] = $class->getStatistic($appendix);


            // statistic 2 same subject id
            $statistic2 = [];
            foreach ($appendix as $key => $value) {
                $statistic2[$key] = [0, 0];
            }
            $classesSameSub = $entityManager->getRepository(ClassSubject::class)->findBy(['idsubject' => $class->getIdsubject()]);

            foreach ($classesSameSub as $item) {
                $retStatistic = $item->getRawStatistic($appendix);
                $rawStatistic = $retStatistic['statistic'];;
                foreach ($rawStatistic as $key => $value) {
                    $statistic2[$key][0] += (float)$value;
                    $statistic2[$key][1] += 1;
                }

            }
            $sta = new Statistic($statistic2);
            try {
                $sta->calculate();
                $classData['statistic']['M2'] = $sta->getAverage();
                $classData['statistic']['STD2'] = $sta->getVariant();
            }catch (\ErrorException $e) {
                $classData['statistic']['M2'] = 0;
                $classData['statistic']['STD2'] = 0;
            }

            $classData['statistic']['M3'] = $M3;
            $classData['statistic']['STD3'] = $STD3;

            $retData[] = $classData;

        }


        return $retData;
    }


}
