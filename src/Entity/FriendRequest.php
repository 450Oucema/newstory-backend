<?php

namespace App\Entity;

use App\Repository\FriendRequestRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FriendRequestRepository::class)
 */
class  FriendRequest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="friendRequests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_asking;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="friendRequests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_responding;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $accepted;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $responseDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    public function __construct()
    {
        $this->created_at = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserAsking(): ?User
    {
        return $this->user_asking;
    }

    public function setUserAsking(?User $user_asking): self
    {
        $this->user_asking = $user_asking;

        return $this;
    }

    public function getUserResponding(): ?User
    {
        return $this->user_responding;
    }

    public function setUserResponding(?User $user_responding): self
    {
        $this->user_responding = $user_responding;

        return $this;
    }

    public function getAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getResponseDate(): ?\DateTimeInterface
    {
        return $this->responseDate;
    }

    public function setResponseDate(?\DateTimeInterface $responseDate): self
    {
        $this->responseDate = $responseDate;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
