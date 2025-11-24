<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SaleRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
#[ORM\Table(name: 'sale')]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $publicId;

    #[ORM\Column]
    private int $totalPrice;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, SaleItem>
     */
    #[ORM\OneToMany(targetEntity: SaleItem::class, mappedBy: 'sale', cascade: ['persist'], orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->totalPrice = 0;
        $this->publicId = Uuid::v4();
    }

    public function getPublicId(): string
    {
        return $this->publicId->toRfc4122();
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * @return Collection<int, SaleItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(SaleItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setSale($this);
        }

        return $this;
    }

    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
