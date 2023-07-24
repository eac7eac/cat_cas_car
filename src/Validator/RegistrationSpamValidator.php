<?php

namespace App\Validator;

use App\Homework\RegistrationSpamFilter;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RegistrationSpamValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\RegistrationSpam $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (! (new RegistrationSpamFilter())->filter($value) ) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
