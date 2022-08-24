<?php

namespace App\Entity;

use App\Repository\AgentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Agent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $first_name = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $last_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Email(message: 'Email format not valid')]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $birth_date = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $mobil = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $social = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $county = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $zip_code = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $license = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $npn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $agency_str = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $phone_ext = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'agent')]
    private ?UsState $state = null;

    #[ORM\ManyToOne(inversedBy: 'agents')]
    private ?Agency $agency = null;

    #[ORM\ManyToOne(inversedBy: 'agents')]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Carrier::class, inversedBy: 'agents')]
    private Collection $agent_carriers;

    #[ORM\ManyToMany(targetEntity: Ancillary::class, inversedBy: 'agents')]
    private Collection $agent_ancilliaries;

    #[ORM\OneToMany(mappedBy: 'agent', targetEntity: Customer::class)]
    private Collection $customers;

    public function __construct()
    {
        $this->agent_carriers = new ArrayCollection();
        $this->agent_ancilliaries = new ArrayCollection();
        $this->customers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthDate(): ?string
    {
        return $this->birth_date;
    }

    public function setBirthDate(?string $birth_date): self
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMobil(): ?string
    {
        return $this->mobil;
    }

    public function setMobil(?string $mobil): self
    {
        $this->mobil = $mobil;

        return $this;
    }

    public function getSocial(): ?string
    {
        return $this->social;
    }

    public function setSocial(?string $social): self
    {
        $this->social = $social;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCounty(): ?string
    {
        return $this->county;
    }

    public function setCounty(?string $county): self
    {
        $this->county = $county;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    public function setZipCode(?string $zip_code): self
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function setLicense(?string $license): self
    {
        $this->license = $license;

        return $this;
    }

    public function getNpn(): ?string
    {
        return $this->npn;
    }

    public function setNpn(?string $npn): self
    {
        $this->npn = $npn;

        return $this;
    }

    public function getAgencyStr(): ?string
    {
        return $this->agency_str;
    }

    public function setAgencyStr(?string $agency_str): self
    {
        $this->agency_str = $agency_str;

        return $this;
    }

    public function getPhoneExt(): ?string
    {
        return $this->phone_ext;
    }

    public function setPhoneExt(?string $phone_ext): self
    {
        $this->phone_ext = $phone_ext;

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

    public function getState(): ?UsState
    {
        return $this->state;
    }

    public function setState(?UsState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    public function setAgency(?Agency $agency): self
    {
        $this->agency = $agency;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Carrier>
     */
    public function getAgentCarriers(): Collection
    {
        return $this->agent_carriers;
    }

    public function addAgentCarrier(Carrier $agentCarrier): self
    {
        if (!$this->agent_carriers->contains($agentCarrier)) {
            $this->agent_carriers->add($agentCarrier);
        }

        return $this;
    }

    public function removeAgentCarrier(Carrier $agentCarrier): self
    {
        $this->agent_carriers->removeElement($agentCarrier);

        return $this;
    }

    /**
     * @return Collection<int, Ancillary>
     */
    public function getAgentAncilliaries(): Collection
    {
        return $this->agent_ancilliaries;
    }

    public function addAgentAncilliary(Ancillary $agentAncilliary): self
    {
        if (!$this->agent_ancilliaries->contains($agentAncilliary)) {
            $this->agent_ancilliaries->add($agentAncilliary);
        }

        return $this;
    }

    public function removeAgentAncilliary(Ancillary $agentAncilliary): self
    {
        $this->agent_ancilliaries->removeElement($agentAncilliary);

        return $this;
    }

    public function __toString() {
        return $this->first_name." ".$this->last_name;
    }

    public function getDisplayName() {
        return $this->first_name." ".$this->last_name;
    }

    /**
     * @return Collection<int, Customer>
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            $customer->setAgent($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getAgent() === $this) {
                $customer->setAgent(null);
            }
        }

        return $this;
    }

}
