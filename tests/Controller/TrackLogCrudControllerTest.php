<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\UserTrackBundle\Controller\TrackLogCrudController;
use Tourze\UserTrackBundle\Entity\TrackLog;

/**
 * 用户轨迹日志管理控制器测试
 * @internal
 */
#[CoversClass(TrackLogCrudController::class)]
#[RunTestsInSeparateProcesses]
class TrackLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): TrackLogCrudController
    {
        return self::getService(TrackLogCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id_header' => ['ID'];
        yield 'user_id_header' => ['用户ID'];
        yield 'event_name_header' => ['事件名称'];
        yield 'event_params_header' => ['事件参数'];
        yield 'source_ip_header' => ['来源IP'];
        yield 'created_at_header' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'user_id_field' => ['userId'];
        yield 'event_field' => ['event'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'user_id_field' => ['userId'];
        yield 'event_field' => ['event'];
    }

    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // 访问新建页面
        $crawler = $client->request('GET', $this->generateAdminUrl(Action::NEW));
        $this->assertResponseIsSuccessful();

        // 验证页面包含表单
        self::assertGreaterThan(0, $crawler->filter('form[name="TrackLog"]')->count(), '新建页面应该包含轨迹日志表单');

        // 获取表单进行提交
        $form = $crawler->filter('form[name="TrackLog"]')->form();

        // 提交空表单验证验证错误
        $crawler = $client->submit($form);

        // 验证返回表单页面（通常是422状态码或重新显示表单）
        self::assertTrue(
            422 === $client->getResponse()->getStatusCode()
            || 200 === $client->getResponse()->getStatusCode(),
            '提交无效表单应返回422状态码或重新显示表单'
        );

        // 验证页面包含验证错误信息
        $pageContent = $crawler->text();
        self::assertTrue(
            str_contains($pageContent, 'should not be blank')
            || str_contains($pageContent, '不能为空')
            || str_contains($pageContent, 'This value should not be blank')
            || str_contains($pageContent, 'required')
            || str_contains($pageContent, '必填'),
            '页面应该显示验证错误信息'
        );

        // 额外通过实体验证测试验证逻辑
        $trackLog = new TrackLog();
        /** @var ValidatorInterface $validator */
        $validator = self::getContainer()->get('validator');
        $violations = $validator->validate($trackLog);
        self::assertGreaterThan(0, count($violations), '空的轨迹日志实体应该有验证错误');
    }

    public function testConfigureFields(): void
    {
        $controller = new TrackLogCrudController();
        $fields = $controller->configureFields('index');

        self::assertIsIterable($fields);
        self::assertNotEmpty($fields);
    }

    /**
     * 测试必填字段验证 - Controller有必填字段event，必须有对应验证测试
     */
    public function testRequiredFieldValidation(): void
    {
        $container = self::getContainer();

        // 创建一个空的轨迹日志实体来测试验证
        $trackLog = new TrackLog();

        // 使用Symfony的验证器测试实体验证
        /** @var ValidatorInterface $validator */
        $validator = $container->get('Symfony\Component\Validator\Validator\ValidatorInterface');
        $violations = $validator->validate($trackLog);

        // 验证必填字段有验证错误
        self::assertGreaterThan(0, count($violations), '轨迹日志实体应该有验证错误（event字段为必填）');

        // 检查是否有event字段的验证错误（NotBlank约束）
        $hasEventError = false;
        foreach ($violations as $violation) {
            if ('event' === $violation->getPropertyPath()) {
                $hasEventError = true;
                // 验证错误消息与NotBlank约束相符
                self::assertNotEmpty($violation->getMessage(), 'event字段验证错误应该有错误消息');
                break;
            }
        }

        self::assertTrue($hasEventError, '应该有event字段的NotBlank验证错误');
    }
}
