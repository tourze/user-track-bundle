<?php

namespace Tourze\UserTrackBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;
use Tourze\UserTrackBundle\Repository\TrackLogRepository;
use Yiisoft\Json\Json;

#[AsScheduleClean(expression: '20 2 * * *', defaultKeepDay: 30, keepDayEnv: 'CLEAN_TRACK_LOG_DAY_NUM')]
#[ORM\Entity(repositoryClass: TrackLogRepository::class)]
#[ORM\Table(name: 'crm_track_log', options: ['comment' => '行为轨迹'])]
class TrackLog implements ApiArrayInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?UserInterface $reporter = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '用户ID'])]
    private ?string $userId = null;

    #[IndexColumn]
    private ?string $event = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '事件参数'])]
    private array $params = [];

    #[CreateIpColumn]
    private ?string $createdFromIp = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeImmutable $createTime = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setCreateTime(?\DateTimeImmutable $createdAt): self
    {
        $this->createTime = $createdAt;

        return $this;
    }

    public function getCreateTime(): ?\DateTimeImmutable
    {
        return $this->createTime;
    }

    public function getReporter(): ?UserInterface
    {
        return $this->reporter;
    }

    public function setReporter(?UserInterface $reporter): self
    {
        $this->reporter = $reporter;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function setParams(?array $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @throws \JsonException
     */
    public function renderParamsColumn(): string
    {
        return Json::encode($this->getParams());
    }

    public function setCreatedFromIp(?string $createdFromIp)
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getCreatedFromIp()
    {
        return $this->createdFromIp;
    }

    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'event' => $this->getEvent(),
            'params' => $this->getParams(),
            'createdFromIp' => $this->getCreatedFromIp(),
        ];
    }

    public function __toString(): string
    {
        return sprintf('TrackLog#%s[%s]', $this->getId() ?? 'new', $this->getEvent() ?? 'no-event');
    }
}
