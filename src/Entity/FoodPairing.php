<?php

namespace App\Entity;

use App\Repository\FoodPairingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FoodPairingRepository::class)
 */
class FoodPairing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Beers::class, inversedBy="foodPairings", cascade={"persist"})
     */
    private $beers;

    public function __construct()
    {
        $this->beers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Beers[]
     */
    public function getBeers(): Collection
    {
        return $this->beers;
    }

    public function addBeer(Beers $beer): self
    {
        if (!$this->beers->contains($beer)) {
            $this->beers[] = $beer;
        }

        return $this;
    }

    public function removeBeer(Beers $beer): self
    {
        $this->beers->removeElement($beer);

        return $this;
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName()
        );
    }
}
