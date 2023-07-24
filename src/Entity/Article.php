<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @Assert\EnableAutoMapping()
 */
class Article
{
    use TimestampableEntity;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Поле Заголовок статьи должно быть заполнено")
     * @Assert\Length(min="3", minMessage="Название статьи должно содержать минимум 3 символа")
     */
    private $title;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\DisableAutoMapping()
     */
    private $slug;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @Assert\DisableAutoMapping()
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     * @Assert\DisableAutoMapping()
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Поле Описание статьи должно быть заполнено")
     * @Assert\Length(max="100", maxMessage="Описание статьи должно содержать максимум 100 символов")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Поле Содержание статьи должно быть заполнено")
     */
    private $body;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\DisableAutoMapping()
     */
    private $keywords;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $voteCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageFilename;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likeCount;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="article", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="articles")
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Поле Автор статьи должно быть заполнено")
     */
    private $author;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getVoteCount(): ?int
    {
        return $this->voteCount;
    }

    public function setVoteCount(?int $voteCount): self
    {
        $this->voteCount = $voteCount;

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function isPublished(): bool
    {
        return null !== $this->getPublishedAt();
    }

    public function getLikeCount(): ?int
    {
        return $this->likeCount;
    }

    public function setLikeCount(?int $likeCount): self
    {
        $this->likeCount = $likeCount;

        return $this;
    }

    public function getAuthorAvatarPath()
    {
        return sprintf(
            'https://robohash.org/%s.png?set=set4',
            str_replace(' ', '_', $this->getAuthor())
        );
    }

    public function like()
    {
        $this->likeCount++;
        return $this;
    }

    public function dislike()
    {
        $this->likeCount--;
        return $this;
    }

    public function voteUp()
    {
        $this->voteCount++;
        return $this;
    }

    public function voteDown()
    {
        $this->voteCount--;
        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getNonDeletedComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        // check if the name is actually a fake name
        if ( preg_match('~[0-9]+~', $this->getTitle()) ) {
            $context->buildViolation('Название статьи не должно содержать цифры')
                ->atPath('title')
                ->addViolation();
        }
    }
}
