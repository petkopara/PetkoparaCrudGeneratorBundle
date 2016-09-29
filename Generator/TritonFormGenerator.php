<?php namespace Petkopara\TritonCrudBundle\Generator;

use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use RuntimeException;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generates a form class based on a Doctrine entity.
 *
 */
class TritonFormGenerator extends Generator
{

    private $className;
    private $classPath;
    private $metadataFactory;

    /**
     * Constructor.
     *
     * @param DisconnectedMetadataFactory $metadataFactory DisconnectedMetadataFactory instance
     */
    public function __construct(DisconnectedMetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getClassPath()
    {
        return $this->classPath;
    }

    /**
     * Generates the entity form class.
     *
     * @param BundleInterface   $bundle         The bundle in which to create the class
     * @param string            $entity         The entity relative class name
     * @param ClassMetadataInfo $metadata       The entity metadata class
     * @param bool              $forceOverwrite If true, remove any existing form class before generating it again
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $forceOverwrite = false)
    {

        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass . 'Type';
        $dirPath = $bundle->getPath() . '/Form';
        $this->classPath = $dirPath . '/' . str_replace('\\', '/', $entity) . 'Type.php';

        if (!$forceOverwrite && file_exists($this->classPath)) {
            throw new RuntimeException(sprintf('Unable to generate the %s form class as it already exists under the %s file', $this->className, $this->classPath));
        }

        if (count($metadata->identifier) > 1) {
            throw new RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        $this->renderFile('form/FormType.php.twig', $this->classPath, array(
            'fields' => $this->getFieldsFromMetadata($metadata),
            'fields_associated' => $this->getAssociatedFields($metadata),
            'fields_mapping' => $metadata->fieldMappings,
            'namespace' => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'entity_class' => $entityClass,
            'bundle' => $bundle->getName(),
            'form_class' => $this->className,
            'form_type_name' => strtolower(str_replace('\\', '_', $bundle->getNamespace()) . ($parts ? '_' : '') . implode('_', $parts) . '_' . substr($this->className, 0, -4)),
            // Add 'setDefaultOptions' method with deprecated type hint, if the new 'configureOptions' isn't available.
            // Required as long as Symfony 2.6 is supported.
            'configure_options_available' => method_exists('Symfony\Component\Form\AbstractType', 'configureOptions'),
            'get_name_required' => !method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix'),
        ));
    }

    /**
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param ClassMetadataInfo $metadata
     *
     * @return array $fields
     */
    private function getFieldsFromMetadata(ClassMetadataInfo $metadata)
    {
        $fields = (array) $metadata->fieldNames;

        // Remove the primary key field if it's not managed manually
        if (!$metadata->isIdentifierNatural()) {
            $fields = array_diff($fields, $metadata->identifier);
        }

        return $fields;
    }

    private function getAssociatedFields(ClassMetadataInfo $metadata)
    {
        $fields = array();

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            
            switch ($relation['type']) {
                case ClassMetadataInfo::ONE_TO_ONE:
                case ClassMetadataInfo::MANY_TO_ONE:
                    $fields[$fieldName] = $this->getRelationFieldData($fieldName, $relation, "MANY_TO_ONE");
                    break;
                case ClassMetadataInfo::MANY_TO_MANY:
                    $fields[$fieldName] = $this->getRelationFieldData($fieldName, $relation, "MANY_TO_MANY");
                    break;
                case ClassMetadataInfo::ONE_TO_MANY:
                    $fields[$fieldName] = $this->getRelationFieldData($fieldName, $relation, "ONE_TO_MANY");
                    break;
            }
        }

        return $fields;
    }

    private function getRelationFieldData($fieldName, $relation, $relationType)
    {
        $field['name'] = $fieldName;
        $field['widget'] = 'EntityType::class';
        $field['class'] = $relation['targetEntity'];
        $field['choice_label'] = $this->guessChoiceLabelFromClass($relation['targetEntity']);
        $field['type'] = $relationType;
        return $field;
    }

    /**
     * Trying to find string field in relation entity. 
     * @param type $entity
     * @return string
     */
    private function guessChoiceLabelFromClass($entity)
    {
        $metadata = $this->metadataFactory->getClassMetadata($entity)->getMetadata();
        foreach ($metadata[0]->fieldMappings as $fieldName => $field) {
            if ($field['type'] == 'string') {
                return $fieldName;
            }
        }
        //if no string field found, return id
        return 'id';
    }
}
