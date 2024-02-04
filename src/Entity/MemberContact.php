<?php

namespace App\Entity;

use App\Model\MemberContactType;
use App\Repository\MemberContactRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: MemberContactRepository::class)]
class MemberContact implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?MemberContactType $type;

    #[ORM\Column(length: 255)]
    private ?string $contactData;

    #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: "contacts")]
    private Member $member;

    public function jsonSerialize(): array
    {
        return [
            'type'  => $this->type->value,
            'value' => $this->contactData,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getType(): ?MemberContactType
    {
        return $this->type;
    }

    public function setType(?MemberContactType $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getContactData(): ?string
    {
        return $this->contactData;
    }

    public function setContactData(?string $contactData): static
    {
        $this->contactData = $contactData;
        return $this;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function setMember(Member $member): static
    {
        $this->member = $member;
        return $this;
    }
}
