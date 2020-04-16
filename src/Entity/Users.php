<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 */
class Users
{
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => ['email','phone'],
            'errorPath' => ['email','phone'],
            'message' => [101,1020],
        ]));

        $metadata->addPropertyConstraint('email', new Assert\Email());
        $metadata->addPropertyConstraint('email', new Assert\Phone());
    }
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"loanStatus", "asset", "friendInfo", "friendRequestInfo", "reportInfo", "userInfo", "loanRequest"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"loanStatus", "friendInfo", "loanRequest", "friendRequestInfo", "reportInfo", "userInfo", "chat", "loaned", "asset"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"loanStatus", "friendInfo", "loanRequest", "friendRequestInfo", "reportInfo", "userInfo", "loaned"})
     */
    private $middleName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"loanStatus", "friendInfo", "loanRequest", "friendRequestInfo", "reportInfo", "userInfo", "chat", "loaned", "asset"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"userInfo"})
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"userInfo"})
     */
    private $address2;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Zipcode", inversedBy="users")
     * @Groups({"userInfo"})
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"friendInfo", "userInfo"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"friendInfo", "userInfo"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"loanStatus, friendInfo", "loanRequest", "userInfo", "asset"})
     */
    private $nickname;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"userInfo"})
     */
    private $birthDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"loanStatus, friendInfo", "userInfo", "asset"})
     */
    private $profileImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"friendInfo", "userInfo"})
     */
    private $usertype;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"friendInfo", "userInfo"})
     */
    private $active;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"friendInfo", "userInfo"})
     */
    private $newsSubscription;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"friendInfo", "userInfo"})
     */
    private $userterms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserConnections", mappedBy="user1")
     */
    private $userConnections;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Assets", mappedBy="users")
     */
    private $assets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Loans", mappedBy="users")
     */
    private $loans;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Chat", mappedBy="user1")
     */
    private $chats;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $authCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserHasUserRights", mappedBy="user")
     */
    private $userHasUserRights;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LogginLogs", mappedBy="users")
     */
    private $logginLogs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UnwantedBehaviorReports", mappedBy="reporter")
     */
    private $unwantedBehaviorReports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AdminSentMails", mappedBy="user")
     */
    private $adminSentMails;

    public function __construct()
    {
        $this->userConnections = new ArrayCollection();
        $this->assets = new ArrayCollection();
        $this->loans = new ArrayCollection();
        $this->chats = new ArrayCollection();
        $this->userHasUserRights = new ArrayCollection();
        $this->logginLogs = new ArrayCollection();
        $this->unwantedBehaviorReports = new ArrayCollection();
        $this->adminSentMails = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getZipCode(): ?Zipcode
    {
        return $this->zipCode;
    }

    public function setZipCode(?Zipcode $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUsertype(): ?string
    {
        return $this->usertype;
    }

    public function setUsertype(string $usertype): self
    {
        $this->usertype = $usertype;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getNewsSubscription(): ?bool
    {
        return $this->newsSubscription;
    }

    public function setNewsSubscription(?bool $newsSubscription): self
    {
        $this->newsSubscription = $newsSubscription;

        return $this;
    }

    public function getUserterms(): ?bool
    {
        return $this->userterms;
    }

    public function setUserterms(?bool $userterms): self
    {
        $this->userterms = $userterms;

        return $this;
    }

    /**
     * @return Collection|UserConnections[]
     */
    public function getUserConnections(): Collection
    {
        return $this->userConnections;
    }

    public function addUserConnection(UserConnections $userConnection): self
    {
        if (!$this->userConnections->contains($userConnection)) {
            $this->userConnections[] = $userConnection;
            $userConnection->setUser1($this);
        }

        return $this;
    }

    public function removeUserConnection(UserConnections $userConnection): self
    {
        if ($this->userConnections->contains($userConnection)) {
            $this->userConnections->removeElement($userConnection);
            // set the owning side to null (unless already changed)
            if ($userConnection->getUser1() === $this) {
                $userConnection->setUser1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Assets[]
     */
    public function getAssets(): Collection
    {
        return $this->assets;
    }

    public function addAsset(Assets $asset): self
    {
        if (!$this->assets->contains($asset)) {
            $this->assets[] = $asset;
            $asset->setUsers($this);
        }

        return $this;
    }

    public function removeAsset(Assets $asset): self
    {
        if ($this->assets->contains($asset)) {
            $this->assets->removeElement($asset);
            // set the owning side to null (unless already changed)
            if ($asset->getUsers() === $this) {
                $asset->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Loans[]
     */
    public function getLoans(): Collection
    {
        return $this->loans;
    }

    public function addLoan(Loans $loan): self
    {
        if (!$this->loans->contains($loan)) {
            $this->loans[] = $loan;
            $loan->setUsers($this);
        }

        return $this;
    }

    public function removeLoan(Loans $loan): self
    {
        if ($this->loans->contains($loan)) {
            $this->loans->removeElement($loan);
            // set the owning side to null (unless already changed)
            if ($loan->getUsers() === $this) {
                $loan->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Chat[]
     */
    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): self
    {
        if (!$this->chats->contains($chat)) {
            $this->chats[] = $chat;
            $chat->setUser1($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chats->contains($chat)) {
            $this->chats->removeElement($chat);
            // set the owning side to null (unless already changed)
            if ($chat->getUser1() === $this) {
                $chat->setUser1(null);
            }
        }

        return $this;
    }

    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }

    public function setAuthCode(?string $authCode): self
    {
        $this->authCode = $authCode;

        return $this;
    }

    /**
     * @return Collection|UserHasUserRights[]
     */
    public function getUserHasUserRights(): Collection
    {
        return $this->userHasUserRights;
    }

    public function addUserHasUserRight(UserHasUserRights $userHasUserRight): self
    {
        if (!$this->userHasUserRights->contains($userHasUserRight)) {
            $this->userHasUserRights[] = $userHasUserRight;
            $userHasUserRight->setUser($this);
        }

        return $this;
    }

    public function removeUserHasUserRight(UserHasUserRights $userHasUserRight): self
    {
        if ($this->userHasUserRights->contains($userHasUserRight)) {
            $this->userHasUserRights->removeElement($userHasUserRight);
            // set the owning side to null (unless already changed)
            if ($userHasUserRight->getUser() === $this) {
                $userHasUserRight->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LogginLogs[]
     */
    public function getLogginLogs(): Collection
    {
        return $this->logginLogs;
    }

    public function addLogginLog(LogginLogs $logginLog): self
    {
        if (!$this->logginLogs->contains($logginLog)) {
            $this->logginLogs[] = $logginLog;
            $logginLog->setUsers($this);
        }

        return $this;
    }

    public function removeLogginLog(LogginLogs $logginLog): self
    {
        if ($this->logginLogs->contains($logginLog)) {
            $this->logginLogs->removeElement($logginLog);
            // set the owning side to null (unless already changed)
            if ($logginLog->getUsers() === $this) {
                $logginLog->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UnwantedBehaviorReports[]
     */
    public function getUnwantedBehaviorReports(): Collection
    {
        return $this->unwantedBehaviorReports;
    }

    public function addUnwantedBehaviorReport(UnwantedBehaviorReports $unwantedBehaviorReport): self
    {
        if (!$this->unwantedBehaviorReports->contains($unwantedBehaviorReport)) {
            $this->unwantedBehaviorReports[] = $unwantedBehaviorReport;
            $unwantedBehaviorReport->setReporter($this);
        }

        return $this;
    }

    public function removeUnwantedBehaviorReport(UnwantedBehaviorReports $unwantedBehaviorReport): self
    {
        if ($this->unwantedBehaviorReports->contains($unwantedBehaviorReport)) {
            $this->unwantedBehaviorReports->removeElement($unwantedBehaviorReport);
            // set the owning side to null (unless already changed)
            if ($unwantedBehaviorReport->getReporter() === $this) {
                $unwantedBehaviorReport->setReporter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AdminSentMails[]
     */
    public function getAdminSentMails(): Collection
    {
        return $this->adminSentMails;
    }

    public function addAdminSentMail(AdminSentMails $adminSentMail): self
    {
        if (!$this->adminSentMails->contains($adminSentMail)) {
            $this->adminSentMails[] = $adminSentMail;
            $adminSentMail->setUser($this);
        }

        return $this;
    }

    public function removeAdminSentMail(AdminSentMails $adminSentMail): self
    {
        if ($this->adminSentMails->contains($adminSentMail)) {
            $this->adminSentMails->removeElement($adminSentMail);
            // set the owning side to null (unless already changed)
            if ($adminSentMail->getUser() === $this) {
                $adminSentMail->setUser(null);
            }
        }

        return $this;
    }
}
