<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\CreatedFromIpAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;
use Tourze\UserTrackBundle\Repository\TrackLogRepository;
use Yiisoft\Json\Json;

/**
 * @implements ApiArrayInterface<string, mixed>
 */
#[AsScheduleClean(expression: '20 2 * * *', defaultKeepDay: 30, keepDayEnv: 'CLEAN_TRACK_LOG_DAY_NUM')]
#[ORM\Entity(repositoryClass: TrackLogRepository::class)]
#[ORM\Table(name: 'crm_track_log', options: ['comment' => '行为轨迹'])]
class TrackLog implements ApiArrayInterface, \Stringable
{
    use SnowflakeKeyAware;
    use CreatedFromIpAware;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?UserInterface $reporter = null;

    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '用户ID'])]
    private ?string $userId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 191)]
    #[IndexColumn]
    #[ORM\Column(length: 191, options: ['comment' => '事件名称'])]
    private ?string $event = null;

    /**
     * @var array<string, mixed>
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '事件参数'])]
    private array $params = [];

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeImmutable $createTime = null;

    public function setCreateTime(?\DateTimeImmutable $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeImmutable
    {
        return $this->createTime;
    }

    public function getReporter(): ?UserInterface
    {
        return $this->reporter;
    }

    public function setReporter(?UserInterface $reporter): void
    {
        $this->reporter = $reporter;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    /**
     * @return array<string, mixed>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @throws \JsonException
     */
    public function renderParamsColumn(): string
    {
        return Json::encode($this->getParams());
    }

    /**
     * @return array<string, mixed>
     */
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
