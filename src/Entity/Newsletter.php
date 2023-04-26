<?php

namespace App\Entity;

use App\Repository\NewsletterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\ByteString;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NewsletterRepository::class)]
#[UniqueEntity('email', message: "L'email {{ value }} est déjà inscrit à la newsletter.")]
#[UniqueEntity('authToken', message: "L'authToken {{ value }} est déjà utilisé.")]
class Newsletter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,unique: true)]
    #[Assert\Email(message: "L'email {{ value }} n'est pas valide.")]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true, unique:true)]
    private ?string $authToken = null;

    #[ORM\Column]
    private ?bool $isActif = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }

    public function setAuthToken(?string $authToken): self
    {
        $this->authToken = $authToken;

        return $this;
    }

    public function isIsActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(bool $isActif): self
    {
        $this->isActif = $isActif;

        return $this;
    }

    public function generateToken(): self
    {
        $this->authToken = ByteString::fromRandom(32)->toString();
        return $this;
    }
}
