<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Product;
use App\Entity\Commande;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 10, options: ['default' => 'light'])]
    private string $theme = 'light';

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Chercheur $chercheur = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Recruteur $recruteur = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Product::class)]
    private \Doctrine\Common\Collections\Collection $products;

    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->commandes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->followers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->following = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = strtolower($email);

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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getChercheur(): ?Chercheur
    {
        return $this->chercheur;
    }

    public function setChercheur(?Chercheur $chercheur): static
    {
        // unset the owning side of the relation if necessary
        if ($chercheur === null && $this->chercheur !== null) {
            $this->chercheur->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($chercheur !== null && $chercheur->getUser() !== $this) {
            $chercheur->setUser($this);
        }

        $this->chercheur = $chercheur;

        return $this;
    }

    public function getRecruteur(): ?Recruteur
    {
        return $this->recruteur;
    }

    public function setRecruteur(?Recruteur $recruteur): static
    {
        // unset the owning side of the relation if necessary
        if ($recruteur === null && $this->recruteur !== null) {
            $this->recruteur->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($recruteur !== null && $recruteur->getUser() !== $this) {
            $recruteur->setUser($this);
        }

        $this->recruteur = $recruteur;

        return $this;
    }
    public function getProducts(): \Doctrine\Common\Collections\Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setUser($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getUser() === $this) {
                $product->setUser(null);
            }
        }

        return $this;
    }

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Commande::class)]
    private \Doctrine\Common\Collections\Collection $commandes;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'following')]
    #[ORM\JoinTable(name: 'user_followers')]
    private \Doctrine\Common\Collections\Collection $followers;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'followers')]
    private \Doctrine\Common\Collections\Collection $following;

    /**
     * @return \Doctrine\Common\Collections\Collection<int, Commande>
     */
    public function getCommandes(): \Doctrine\Common\Collections\Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<int, self>
     */
    public function getFollowers(): \Doctrine\Common\Collections\Collection
    {
        return $this->followers;
    }

    public function addFollower(self $follower): static
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
        }

        return $this;
    }

    public function removeFollower(self $follower): static
    {
        $this->followers->removeElement($follower);

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<int, self>
     */
    public function getFollowing(): \Doctrine\Common\Collections\Collection
    {
        return $this->following;
    }

    public function follow(self $user): static
    {
        if (!$this->following->contains($user)) {
            $this->following->add($user);
            $user->addFollower($this);
        }

        return $this;
    }

    public function unfollow(self $user): static
    {
        if ($this->following->removeElement($user)) {
            $user->removeFollower($this);
        }

        return $this;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }
}
