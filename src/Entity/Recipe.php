<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecipeRepository::class)
 */
class Recipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @ORM\Column(type="smallint")
     */
    private $number_of_persons;

    /**
     * @ORM\Column(type="float")
     */
    private $difficulty;

    /**
     * @ORM\Column(type="integer")
     */
    private $preparation_time;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_time;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_id;

    /**
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="recette")
     */
    private $ratings;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=RecipeSteps::class, mappedBy="recipe", orphanRemoval=true)
     */
    private $recipeSteps;

    /**
     * @ORM\OneToMany(targetEntity=RecipeTagsLinks::class, mappedBy="recipe", orphanRemoval=true)
     */
    private $recipeTagsLinks;

    /**
     * @ORM\OneToMany(targetEntity=RecipeIngredients::class, mappedBy="recipe", orphanRemoval=true)
     */
    private $ingredients;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="recette")
     */
    private $comments;
  
    public function __construct()
    {
        $this->recipeSteps = new ArrayCollection();
        $this->recipeTagsLinks = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNumberOfPersons(): ?int
    {
        return $this->number_of_persons;
    }

    public function setNumberOfPersons(int $number_of_persons): self
    {
        $this->number_of_persons = $number_of_persons;

        return $this;
    }

    public function getDifficulty(): ?float
    {
        return $this->difficulty;
    }

    public function setDifficulty(float $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection|Rating[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setRecette($this);
        }

        return $this;
    }

    public function getPreparationTime(): ?string
    {
        return $this->secondsToTime($this->preparation_time*60);
    }

    public function setPreparationTime(int $preparation_time): self
    {
      $this->preparation_time = $preparation_time;

      return $this;
    }
      
    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getRecette() === $this) {
                $rating->setRecette(null);
            }
        }

        return $this;
    }

    public function getTotalTime(): ?string
    {
        return $this->secondsToTime($this->total_time*60);
    }

    public function setTotalTime(int $total_time): self
    {
        $this->total_time = $total_time;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setRecette($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getRecette() === $this) {
                $comment->setRecette(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RecipeSteps[]
     */
    public function getRecipeSteps(): Collection
    {
        return $this->recipeSteps;
    }

    public function addRecipeStep(RecipeSteps $recipeStep): self
    {
        if (!$this->recipeSteps->contains($recipeStep)) {
            $this->recipeSteps[] = $recipeStep;
            $recipeStep->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipeStep(RecipeSteps $recipeStep): self
    {
        if ($this->recipeSteps->removeElement($recipeStep)) {
            // set the owning side to null (unless already changed)
            if ($recipeStep->getRecipe() === $this) {
                $recipeStep->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RecipeTagsLinks[]
     */
    public function getRecipeTagsLinks(): Collection
    {
        return $this->recipeTagsLinks;
    }

    public function addRecipeTagsLink(RecipeTagsLinks $recipeTagsLink): self
    {
        if (!$this->recipeTagsLinks->contains($recipeTagsLink)) {
            $this->recipeTagsLinks[] = $recipeTagsLink;
            $recipeTagsLink->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipeTagsLink(RecipeTagsLinks $recipeTagsLink): self
    {
        if ($this->recipeTagsLinks->removeElement($recipeTagsLink)) {
            // set the owning side to null (unless already changed)
            if ($recipeTagsLink->getRecipe() === $this) {
                $recipeTagsLink->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RecipeIngredients[]
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(RecipeIngredients $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->setRecipe($this);
        }

        return $this;
    }

    public function removeIngredient(RecipeIngredients $ingredient): self
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getRecipe() === $this) {
                $ingredient->setRecipe(null);
            }
        }

        return $this;
    }

    private function secondsToTime($seconds) {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        if ($seconds >= 86400){
            return $dtF->diff($dtT)->format('%a j %h h %i min');
        }else{
            return $dtF->diff($dtT)->format('%h h %i min');
        }
    }
}
