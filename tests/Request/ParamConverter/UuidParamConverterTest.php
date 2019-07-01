<?php

namespace SuperBrave\UtilityPackage\Tests\Request\ParamConverter;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use SuperBrave\UtilityPackage\Request\ParamConverter\UuidParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * UuidParamConverterTest.
 */
class UuidParamConverterTest extends TestCase
{
    /**
     * @var ParamConverter
     */
    private $configuration;

    /**
     * @var UuidParamConverter
     */
    private $paramConverter;

    /**
     * @var Request
     */
    private $request;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->configuration = new ParamConverter(array(
            'name' => 'uuid',
            'class' => UuidInterface::class,
        ));

        $this->paramConverter = new UuidParamConverter();
        $this->request = new Request();
    }

    /**
     * Tests if an valid uuid is created from an valid string.
     */
    public function testApply()
    {
        $this->request->attributes->set('uuid', 'ab101d76-c14e-4e51-971f-c837f1fe30fa');

        $this->assertTrue(
            $this->paramConverter->apply($this->request, $this->configuration)
        );

        $this->assertInstanceOf(UuidInterface::class, $this->request->attributes->get('uuid'));
    }

    /**
     * Tests if UuidParamConverter::apply returns false when the parameter is optional and not set.
     */
    public function testApplyOptionalWithoutAttribute()
    {
        $this->configuration->setIsOptional(true);

        $this->assertFalse(
            $this->paramConverter->apply($this->request, $this->configuration)
        );

        $this->assertNull($this->request->attributes->get('uuid'));
    }

    /**
     * Tests if an valid uuid is created from an invalid string.
     */
    public function testApplyWithAnInvalidUuid()
    {
        $this->request->attributes->set('uuid', 'invalid-uuid');

        // a BadRequestHttpException should be thrown
        $this->expectException(BadRequestHttpException::class);

        // apply an invalid uuid
        $this->paramConverter->apply($this->request, $this->configuration);
    }

    /**
     * Tests if the generated configuration gets supported with an valid name.
     */
    public function testSupports()
    {
        $supports = $this->paramConverter
            ->supports($this->configuration);

        $this->assertTrue($supports);
    }

    /**
     * Tests if the configuration is not supported with an invalid uuid.
     */
    public function testSupportWithAnInvalidClass()
    {
        $this->configuration->setClass('test');

        $supports = $this->paramConverter
            ->supports($this->configuration);

        $this->assertFalse($supports);
    }
}
