<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class UniqueEntityByUser extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'this "{{ value }}" already exist.';
    public string $field;
    public string $entityClass;

    #[HasNamedArguments]
    public function __construct(
        string $field,
        string $entityClass,
        array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([
            'field' => $field,
            'entityClass' => $entityClass
        ], $groups, $payload);
        $this->field = $field;
        $this->entityClass = $entityClass;
    }

    public function getRequiredOptions()
    {
        return [
            'field',
            'entityClass'
        ];
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
