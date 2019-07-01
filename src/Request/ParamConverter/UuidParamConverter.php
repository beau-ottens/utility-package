<?php

namespace SuperBrave\UtilityPackage\Request\ParamConverter;

use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Converts UUID string parameters in a Request to a @see Uuid instance.
 */
class UuidParamConverter implements ParamConverterInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws BadRequestHttpException when an invalid UUID string is provided
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $param = $configuration->getName();
        if ($configuration->isOptional() && $request->attributes->has($param) === false) {
            return false;
        }

        $value = $request->attributes->get($param);

        try {
            $uuid = Uuid::fromString($value);

            $request->attributes->set($param, $uuid);

            return true;
        } catch (InvalidUuidStringException $e) {
            throw new BadRequestHttpException(sprintf("Invalid UUID '%s' provided.", $value));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return UuidInterface::class === $configuration->getClass();
    }
}
