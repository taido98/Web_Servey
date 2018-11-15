<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileSurveyRepository")
 */
class FileSurvey
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $brochure;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrochure(): ?string
    {
        return $this->brochure;
    }

    public function setBrochure(?string $brochure): self
    {
        $this->brochure = $brochure;

        return $this;
    }
}
