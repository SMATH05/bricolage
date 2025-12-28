<?php

namespace App\Entity;

use App\Repository\CandidatureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatureRepository::class)]
class Candidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $id_candidature = null;

    #[ORM\ManyToOne(inversedBy: 'candidatures')]
    private ?Chercheur $chercheur_id = null;

    #[ORM\Column]
    private ?\DateTime $date_pro = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'candidatures')]
    private ?Annonce $annonce_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCandidature(): ?string
    {
        return $this->id_candidature;
    }

    public function setIdCandidature(string $id_candidature): static
    {
        $this->id_candidature = $id_candidature;

        return $this;
    }

    public function getChercheurId(): ?Chercheur
    {
        return $this->chercheur_id;
    }

    public function setChercheurId(?Chercheur $chercheur_id): static
    {
        $this->chercheur_id = $chercheur_id;

        return $this;
    }

    public function getDatePro(): ?\DateTime
    {
        return $this->date_pro;
    }

    public function setDatePro(\DateTime $date_pro): static
    {
        $this->date_pro = $date_pro;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getAnnonceId(): ?Annonce
    {
        return $this->annonce_id;
    }

    public function setAnnonceId(?Annonce $annonce_id): static
    {
        $this->annonce_id = $annonce_id;

        return $this;
    }
}
