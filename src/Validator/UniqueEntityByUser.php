<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class UniqueEntityByUser extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'this "{{ value }}" already exist.';
    public string $field;
    public string $mappingOwner;
    public string $entityClass;


    #[HasNamedArguments]
    public function __construct(
        string $field,
        string $mappingOwner,
        string $entityClass,
        array $groups = null,
        mixed $payload = null,
    ) {

        parent::__construct([
            'field' => $field,
            'mappingOwner' => $mappingOwner,
            'entityClass' => $entityClass
        ], $groups, $payload);

        $this->field = $field;
        $this->mappingOwner = $mappingOwner;
        $this->entityClass = $entityClass;
    }

    /**
     * @return string[]
     */
    public function getRequiredOptions()
    {
        return [
            'field',
            'mappingOwner',
            'entityClass',
        ];
    }

    /**
     * @return string|string[] One or more constant values
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
