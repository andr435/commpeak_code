<?php

namespace App\Entity;

use App\Repository\CodeToContinentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CodeToContinentRepository::class)]
class CodeToContinent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 2)]
    private ?string $continent = null;

    #[ORM\Column]
    private ?int $phone_code = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContinent(): ?string
    {
        return $this->continent;
    }

    public function setContinent(string $continent): static
    {
        $this->continent = $continent;

        return $this;
    }

    public function getPhoneCode(): ?int
    {
        return $this->phone_code;
    }

    public function setPhoneCode(int $phone_code): static
    {
        $this->phone_code = $phone_code;

        return $this;
    }
}
