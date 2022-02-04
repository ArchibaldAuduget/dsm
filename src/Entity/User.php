<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: ArtistRequest::class, cascade: ['persist', 'remove'])]
    private $artistRequest;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Artist::class, cascade: ['persist', 'remove'])]
    private $artist;

    #[ORM\OneToMany(mappedBy: 'admin', targetEntity: Artist::class)]
    private $artists;

    #[ORM\OneToMany(mappedBy: 'admin', targetEntity: ArtistRequest::class)]
    private $artistRequests;

    #[ORM\ManyToMany(targetEntity: Music::class, inversedBy: 'users')]
    private $favorites;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Playlist::class)]
    private $playlist;

    public function __construct()
    {
        $this->artists = new ArrayCollection();
        $this->artistRequests = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->playlist = new ArrayCollection();
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
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getArtistRequest(): ?ArtistRequest
    {
        return $this->artistRequest;
    }

    public function setArtistRequest(ArtistRequest $artistRequest): self
    {
        // set the owning side of the relation if necessary
        if ($artistRequest->getUser() !== $this) {
            $artistRequest->setUser($this);
        }

        $this->artistRequest = $artistRequest;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(Artist $artist): self
    {
        // set the owning side of the relation if necessary
        if ($artist->getUser() !== $this) {
            $artist->setUser($this);
        }

        $this->artist = $artist;

        return $this;
    }

    /**
     * @return Collection|Artist[]
     */
    public function getArtists(): Collection
    {
        return $this->artists;
    }

    /**
     * @return Collection|ArtistRequest[]
     */
    public function getArtistRequests(): Collection
    {
        return $this->artistRequests;
    }

    public function addArtistRequest(ArtistRequest $artistRequest): self
    {
        if (!$this->artistRequests->contains($artistRequest)) {
            $this->artistRequests[] = $artistRequest;
            $artistRequest->setAdmin($this);
        }

        return $this;
    }

    public function removeArtistRequest(ArtistRequest $artistRequest): self
    {
        if ($this->artistRequests->removeElement($artistRequest)) {
            // set the owning side to null (unless already changed)
            if ($artistRequest->getAdmin() === $this) {
                $artistRequest->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Music[]
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Music $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
        }

        return $this;
    }

    public function removeFavorite(Music $favorite): self
    {
        $this->favorites->removeElement($favorite);

        return $this;
    }

    /**
     * @return Collection|Playlist[]
     */
    public function getPlaylist(): Collection
    {
        return $this->playlist;
    }

    public function addPlaylist(Playlist $playlist): self
    {
        if (!$this->playlist->contains($playlist)) {
            $this->playlist[] = $playlist;
            $playlist->setUser($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): self
    {
        if ($this->playlist->removeElement($playlist)) {
            // set the owning side to null (unless already changed)
            if ($playlist->getUser() === $this) {
                $playlist->setUser(null);
            }
        }

        return $this;
    }
}
