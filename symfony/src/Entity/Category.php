<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Listing::class, mappedBy: 'category')]
    private Collection $listings;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->listings = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getListings(): Collection
    {
        return $this->listings;
    }

    /**
     * @param Collection $listings
     * @return $this
     */
    public function setListings(Collection $listings): static
    {
        $this->listings = $listings;

        return $this;
    }

    /**
     * @return mixed
     */
    #[ArrayShape([
        'id'   => "int|null",
        'name' => "null|string"
    ])] public function jsonSerialize(): mixed
    {
        return [
            'id'   => $this->getId(),
            'name' => $this->getName()
        ];
    }
}
