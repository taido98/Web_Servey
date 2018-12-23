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
        // statistic 3 all class same teacher
        $classesSameTeacher = $entityManager->getRepository(ClassSubject::class)->findBy(['teacher' => $this]);
        $statistic3 = [];
        foreach ($appendix as $key => $value) {
            $statistic[$key] = [];
        }

        foreach ($classesSameTeacher as $item) {
            $retStatistic = $item->getRawStatistic($appendix);
            $rawStatistic = $retStatistic['statistic'];
            foreach ($rawStatistic as $key => $value) {
                $data = [];
                foreach ($value as $k => $v) {
                    $data[] = [(int) $k, (int) $v];
                }
                $sta = new Statistic($data);
                try {
                    $sta->calculate();
                    $statistic3[$key]['M3'] = round($sta->getAverage(), 2);
                    $statistic3[$key]['STD3'] = round($sta->getAverage(), 2);

                } catch (\ErrorException $e) {
                    $statistic3[$key]['M'] = 0;
                    $statistic3[$key]['STD'] = 0;
                }
            }

        }


        // statistic 1 each class
        foreach ($this->teacher as $class) {
            $classData = $class->toString();
            $classData['statistic'] = $class->getStatistic($appendix)['statistic'];


            // statistic 2 same subject id
            $classesSameSub = $entityManager->getRepository(ClassSubject::class)->findBy(['idsubject' => $class->getIdsubject()]);

            foreach ($classesSameSub as $item) {
                $retStatistic = $item->getRawStatistic($appendix);
                $rawStatistic = $retStatistic['statistic'];;
                foreach ($rawStatistic as $key => $value) {
                    $data = [];
                    foreach ($value as $k => $v) {
                        $data[] = [(int) $k, (int) $v];
                    }
                    $sta = new Statistic($data);
                    try {
                        $sta->calculate();
                        $classData['statistic'][$key]['M2'] = round($sta->getAverage(), 2);
                        $classData['statistic'][$key]['STD2'] = round($sta->getAverage(), 2);

                    } catch (\ErrorException $e) {
                        $classData['statistic'][$key]['M2'] = 0;
                        $classData['statistic'][$key]['STD2'] = 0;
                    }
                }

            }
            foreach ($statistic3 as $key=>$value) {
                $classData['statistic'][$key]['M3'] = $statistic3[$key]['M3'];
                $classData['statistic'][$key]['STD3'] = $statistic3[$key]['STD3'];
            }





            $retData[] = $classData;

        }

        return $retData;
    }
}
