<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("comment:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("comment:read")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups("comment:read")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Tricks::class, inversedBy="comments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("comment:read")
     */
    private $tricks;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups("comment:read")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTricks(): ?Tricks
    {
        return $this->tricks;
    }

    public function setTricks(?Tricks $tricks): self
    {
        $this->tricks = $tricks;

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
}
