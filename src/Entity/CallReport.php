<?php

namespace App\Entity;


use App\Repository\CallReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CodeToContinent;
use App\Service\IpGeolocation;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;


#[ORM\Entity(repositoryClass: CallReportRepository::class)]
#[ORM\Table(indexes: [new ORM\Index(name: "idx_customer", columns: ["customer_id"])])]
class CallReport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $customer_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $call_date = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(length: 20)]
    private ?string $dialed_number = null;

    #[ORM\Column(length: 40)]
    private ?string $customer_ip = null;

    #[ORM\Column]
    private ?bool $internal_call = null;

    private EntityManagerInterface $entity_manager;

    private FilesystemAdapter $cache;

    public function __construct(EntityManagerInterface $entity_manager)
    {
        $this->entity_manager = $entity_manager;
        $this->cache = new FilesystemAdapter();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerId(): ?int
    {
        return $this->customer_id;
    }

    public function setCustomerId(int $customer_id): static
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    public function getCallDate(): ?\DateTimeInterface
    {
        return $this->call_date;
    }

    public function setCallDate(\DateTimeInterface $call_date): static
    {
        $this->call_date = $call_date;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDialedNumber(): ?string
    {
        return $this->dialed_number;
    }

    public function setDialedNumber(string $dialed_number): static
    {
        $this->dialed_number = $dialed_number;
        $this->setInternalCall();
        
        return $this;
    }

    public function getCustomerIp(): ?string
    {
        return $this->customer_ip;
    }

    public function setCustomerIp(string $customer_ip): static
    {
        $this->customer_ip = $customer_ip;
        $this->setInternalCall();
        
        return $this;
    }

    public function isInternalCall(): ?bool
    {
        return $this->internal_call;
    }

    protected function setInternalCall(): static
    {
        if(!empty($this->customer_ip && !empty($this->dialed_number))){
            $customer_continent = $this->getCustomerContinent();
            $call_continent = $this->getCallContinent();
            $this->internal_call = $customer_continent === $call_continent;
        }
        return $this;
    }
    
    public function save(): void
    {
        $this->entity_manager->persist($this);
        $this->entity_manager->flush();
    }

    private function getCustomerContinent(): string
    {
        $continent = $this->cache->get('customer_continent_'.$this->customer_ip, function (ItemInterface $item, $phone_prefix): string|null    
        {
            $item->expiresAfter(360000);
            $geolocation = new IpGeolocation();
            return $geolocation->getContinent($this->customer_ip);
        });
        
        return $continent;
    }

    private function getCallContinent(): string
    {
        $phone_prefix = substr($this->dialed_number, 0, 3);
        
        $continent = $this->cache->get('dialed_continent_'.$phone_prefix, function (ItemInterface $item, $phone_prefix): string|null    
        {
            $item->expiresAfter(360000);
            $continent = $this->entity_manager
            ->getRepository(CodeToContinent::class)
            ->findOneBy([
                'phone_code' => $phone_prefix
            ]);    
            return $continent->getContinent() ?? null;
        });
        
        return $continent;
    }
}
