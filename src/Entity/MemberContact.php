<?php

namespace App\Entity;

use App\Model\MemberContactType;
use App\Repository\MemberContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;

#[ORM\Entity(repositoryClass: MemberContactRepository::class)]
class MemberContact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['list'])]
    // it serializes into {name: string, value: string}
    // not sure, this is what you meant
    #[Context(denormalizationContext: [BackedEnumNormalizer::class])]
    private ?MemberContactType $type;

    #[ORM\Column(length: 255)]
    #[Groups(['list'])]
    private ?string $contactData;

    #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: "contacts")]
    #[Ignore] // otherwise cyclic ref error - maybe it can be done with maxDepth stuff, good enough for now
    // something is fishy, the data disappeared from the response // TODO: later check enum
    private Member $member;

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
