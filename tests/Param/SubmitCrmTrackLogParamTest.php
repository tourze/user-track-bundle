<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\UserTrackBundle\Param\SubmitCrmTrackLogParam;

/**
 * SubmitCrmTrackLogParam 单元测试
 *
 * @internal
 */
#[CoversClass(SubmitCrmTrackLogParam::class)]
final class SubmitCrmTrackLogParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new SubmitCrmTrackLogParam(event: 'click_button');

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithRequiredParameterOnly(): void
    {
        $param = new SubmitCrmTrackLogParam(event: 'page_view');

        $this->assertSame('page_view', $param->event);
        $this->assertSame([], $param->params);
    }

    public function testConstructorWithAllParameters(): void
    {
        $params = ['button_id' => 123, 'page' => 'home'];
        $param = new SubmitCrmTrackLogParam(
            event: 'click_button',
            params: $params,
        );

        $this->assertSame('click_button', $param->event);
        $this->assertSame($params, $param->params);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(SubmitCrmTrackLogParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(SubmitCrmTrackLogParam::class);

        $properties = ['event', 'params'];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isPublic(), "{$propertyName} should be public");
            $this->assertTrue($property->isReadOnly(), "{$propertyName} should be readonly");
        }
    }

    public function testValidationFailsWhenEventIsBlank(): void
    {
        $param = new SubmitCrmTrackLogParam(event: '');

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $violations = $validator->validate($param);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testValidationPassesWithValidParameters(): void
    {
        $param = new SubmitCrmTrackLogParam(
            event: 'user_action',
            params: ['action' => 'submit'],
        );

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $violations = $validator->validate($param);

        $this->assertCount(0, $violations);
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(SubmitCrmTrackLogParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        foreach ($constructor->getParameters() as $parameter) {
            $attrs = $parameter->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
            $this->assertNotEmpty($attrs, "Parameter {$parameter->getName()} should have MethodParam attribute");
        }
    }
}
