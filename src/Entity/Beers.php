<?php

namespace App\Entity;

use App\Repository\BeersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BeersRepository::class)
 */
class Beers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slogan;

    /**
     * @ORM\Column(type="date")
     */
    private $first_brewed;

    /**
     * @ORM\ManyToMany(targetEntity=FoodPairing::class, mappedBy="beers")
     */
    private $foodPairings;

    public function __construct()
    {
        $this->foodPairings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    public function setSlogan(string $slogan): self
    {
        $this->slogan = $slogan;

        return $this;
    }

    public function getFirstBrewed(): ?\DateTimeInterface
    {
        return $this->first_brewed;
    }

    public function setFirstBrewed(\DateTimeInterface $first_brewed): self
    {
        $this->first_brewed = $first_brewed;

        return $this;
    }

    /**
     * @return Collection|FoodPairing[]
     */
    public function getFoodPairings(): Collection
    {
        return $this->foodPairings;
    }

    public function addFoodPairing(FoodPairing $foodPairing): self
    {
        if (!$this->foodPairings->contains($foodPairing)) {
            $this->foodPairings[] = $foodPairing;
            $foodPairing->addBeer($this);
        }

        return $this;
    }

    public function removeFoodPairing(FoodPairing $foodPairing): self
    {
        if ($this->foodPairings->removeElement($foodPairing)) {
            $foodPairing->removeBeer($this);
        }

        return $this;
    }

    public function toArray()
    {
        $foods = array();
        foreach($this->getFoodPairings() as $food) {
            $foods[] = $food->toArray();
        }
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'image' => $this->getImage(),
            'slogan' => $this->getSlogan(),
            'date' => $this->getFirstBrewed()
        );
    }
}
