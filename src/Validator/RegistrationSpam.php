<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class RegistrationSpam extends Constraint
{
    public $message = 'Ботам "{{ value }}" здесь не место';
}
