<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Piece::class, mappedBy="user")
     */
    private $pieces;

    /**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="user")
     */
    private $produits;

    /**
     * @ORM\Column(type="boolean")
     */
    private $private;

    /**
     * @ORM\Column(type="boolean")
     */
    private $private_for_friends;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="friends")
     */
    private $friends;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profilePictureUrl;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity=FriendRequest::class, mappedBy="user_responding")
     */
    private $friendRequests;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    /**
     * @ORM\OneToMany(targetEntity=Alert::class, mappedBy="user")
     */
    private $alerts;

    public function __construct()
    {
        $this->pieces = new ArrayCollection();
        $this->produits = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->friendRequests = new ArrayCollection();
        $this->alerts = new ArrayCollection();
    }

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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Piece[]
     */
    public function getPieces(): Collection
    {
        return $this->pieces;
    }

    public function addPiece(Piece $piece): self
    {
        if (!$this->pieces->contains($piece)) {
            $this->pieces[] = $piece;
            $piece->setUser($this);
        }

        return $this;
    }

    public function removePiece(Piece $piece): self
    {
        if ($this->pieces->contains($piece)) {
            $this->pieces->removeElement($piece);
            // set the owning side to null (unless already changed)
            if ($piece->getUser() === $this) {
                $piece->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setUser($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->contains($produit)) {
            $this->produits->removeElement($produit);
            // set the owning side to null (unless already changed)
            if ($produit->getUser() === $this) {
                $produit->setUser(null);
            }
        }

        return $this;
    }

    public function getPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;

        return $this;
    }

    public function getPrivateForFriends(): ?bool
    {
        return $this->private_for_friends;
    }

    public function setPrivateForFriends(bool $private_for_friends): self
    {
        $this->private_for_friends = $private_for_friends;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(self $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends[] = $friend;
        }

        return $this;
    }

    public function removeFriend(self $friend): self
    {
        if ($this->friends->contains($friend)) {
            $this->friends->removeElement($friend);
        }

        return $this;
    }

    public function isFriend(self $friend): bool {
        if ($this->friends->contains($friend)) {
            return true;
        }

        return false;
    }

    public function getProfilePictureUrl(): ?string
    {
        return $this->profilePictureUrl;
    }

    public function setProfilePictureUrl(?string $profilePictureUrl): self
    {
        $this->profilePictureUrl = $profilePictureUrl;

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

    /**
     * @return Collection|FriendRequest[]
     */
    public function getFriendRequests(): Collection
    {
        return $this->friendRequests;
    }

    public function addFriendRequest(FriendRequest $friendRequest): self
    {
        if (!$this->friendRequests->contains($friendRequest)) {
            $this->friendRequests[] = $friendRequest;
            $friendRequest->setUserAsking($this);
        }

        return $this;
    }

    public function removeFriendRequest(FriendRequest $friendRequest): self
    {
        if ($this->friendRequests->contains($friendRequest)) {
            $this->friendRequests->removeElement($friendRequest);
            // set the owning side to null (unless already changed)
            if ($friendRequest->getUserAsking() === $this) {
                $friendRequest->setUserAsking(null);
            }
        }

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

    /**
     * @return Collection|Alert[]
     */
    public function getAlerts(): Collection
    {
        return $this->alerts;
    }

    public function addAlert(Alert $alert): self
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts[] = $alert;
            $alert->setUser($this);
        }

        return $this;
    }

    public function removeAlert(Alert $alert): self
    {
        if ($this->alerts->contains($alert)) {
            $this->alerts->removeElement($alert);
            // set the owning side to null (unless already changed)
            if ($alert->getUser() === $this) {
                $alert->setUser(null);
            }
        }

        return $this;
    }
}
