<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    /**
     * @Assert\NotBlank
     */
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email(message: 'Email format not valid')]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(min: 6, max: 255)]
    private ?string $password = null;

    /**
     * @Assert\NotBlank
     */
    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    /**
     * @Assert\NotBlank
     */
    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[Assert\NotBlank]
    #[Assert\Count(min: 1,minMessage: 'You have to select at least one Role')]
    private Collection $user_roles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Agent::class)]
    private Collection $agents;

    /**
     * User constructor.
     * @param int|null $id
     * @param string|null $email
     * @param array $roles
     * @param string|null $password
     */
    public function __construct(?string $first_name=null, ?string $last_name=null, ?int $id=null, ?string $email=null,  ?string $password=null)
    {
        $this->first_name=$first_name;
        $this->last_name=$last_name;
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->user_roles = new ArrayCollection();
        $this->agents = new ArrayCollection();
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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $rolesList=$this->getUserRoles();
        $roles=array();

        if($rolesList!==null or $rolesList!==""){
            foreach ($rolesList as $rol){
                $roles[]=$rol->getName();
            }
        }

        return array_unique($roles);
    }

    /**
     * @see UserInterface
     */
    public function hasRole(String $role): bool
    {
        $rolesList=$this->getUserRoles();

        if($rolesList!==null or $rolesList!==""){
            foreach ($rolesList as $rol){
                if($rol->getName() == $role){
                    return true;
                }
            }
        }

        return false;
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
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullName(){
        return $this->first_name.' '.$this->last_name;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->created_at = new \DateTime();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getUserRoles(): Collection
    {
        return $this->user_roles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->user_roles->contains($userRole)) {
            $this->user_roles[] = $userRole;
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        $this->user_roles->removeElement($userRole);

        return $this;
    }

    public function __toString() {
        return $this->first_name.' '.$this->last_name;
    }

    public function getDisplayName() {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * @return Collection<int, Agent>
     */
    public function getAgents(): Collection
    {
        return $this->agents;
    }

    public function addAgent(Agent $agent): self
    {
        if (!$this->agents->contains($agent)) {
            $this->agents->add($agent);
            $agent->setUser($this);
        }

        return $this;
    }

    public function removeAgent(Agent $agent): self
    {
        if ($this->agents->removeElement($agent)) {
            // set the owning side to null (unless already changed)
            if ($agent->getUser() === $this) {
                $agent->setUser(null);
            }
        }

        return $this;
    }

}
