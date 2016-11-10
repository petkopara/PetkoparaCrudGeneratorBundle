<?php

namespace Petkopara\CrudGeneratorBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Petkopara\CrudGeneratorBundle\Command\CrudGeneratorCommand;
use Petkopara\CrudGeneratorBundle\Generator\Guesser\MetadataGuesser;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Exception\RuntimeException;

/**
 * Generates a form Filter class based on a Doctrine entity.
 */
class PetkoparaFilterGenerator extends Generator
{

    private $className;
    private $classPath;
    private $metadataGuesser;

    /**
     * 
     * @param MetadataGuesser $guesser
     */
    public function __construct(MetadataGuesser $guesser)
    {
        $this->metadataGuesser = $guesser;
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
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface $bundle The bundle in which to create the class
     * @param string $entity The entity relative class name
     * @param ClassMetadataInfo $metadata The entity metadata class
     * @param $forceOverwrite
     *
     * @throws RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $forceOverwrite, $type)
    {
        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass . 'FilterType';
        $dirPath = $bundle->getPath() . '/Form';
        $this->classPath = $dirPath . '/' . str_replace('\\', '/', $entity) . 'FilterType.php';

        if (!$forceOverwrite && file_exists($this->classPath)) {
            throw new RuntimeException(sprintf('Unable to generate the %s form class as it already exists under the %s file', $this->className, $this->classPath));
        }

        if (count($metadata->identifier) > 1) {
            throw new RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        if ($type == CrudGeneratorCommand::FILTER_TYPE_FORM) {


            $this->renderFile('form/FormFilterType.php.twig', $this->classPath, array(
                'fields_data' => $this->getFieldsDataFromMetadata($metadata),
                'fields_associated' => $this->getAssociatedFields($metadata),
                'namespace' => $bundle->getNamespace(),
                'entity_namespace' => implode('\\', $parts),
                'entity_class' => $entityClass,
                'bundle' => $bundle->getName(),
                'form_class' => $this->className,
                'form_filter_type_name' => strtolower(str_replace('\\', '_', $bundle->getNamespace()) . ($parts ? '_' : '') . implode('_', $parts) . '_' . $this->className),
            ));
        } else { //multi search input 
            $this->renderFile('form/FormMultiSearchFilter.php.twig', $this->classPath, array(
                'namespace' => $bundle->getNamespace(),
                'fields_data' => $this->getFieldsDataFromMetadata($metadata),
                'entity_namespace' => implode('\\', $parts),
                'entity_class' => $entityClass,
                'bundle' => $bundle->getName(),
                'form_class' => $this->className,
                'form_filter_type_name' => strtolower(str_replace('\\', '_', $bundle->getNamespace()) . ($parts ? '_' : '') . implode('_', $parts) . '_' . $this->className),
            ));
        }
    }

    /**
     * Returns an array of fields data (name and filter widget to use).
     * Fields can be both column fields and association fields.
     *
     * @param ClassMetadataInfo $metadata
     * @return array $fields
     */
    private function getFieldsDataFromMetadata(ClassMetadataInfo $metadata)
    {
        $fieldsData = (array) $metadata->fieldMappings;
        $fieldsResult = array();
        // Convert type to filter widget
        foreach ($fieldsData as $fieldName => $data) {
            $fieldWidget = $this->getFilterType($fieldsData[$fieldName]['type']);
            if ($fieldWidget!== false) {
                $fieldsResult[$fieldName]['fieldName'] = $fieldName;
                $fieldsResult[$fieldName]['filterWidget'] = $this->getFilterType($fieldsData[$fieldName]['type'], $fieldName);
            }
        }

        return $fieldsResult;
    }

    private function getFilterType($dbType)
    {
        switch ($dbType) {
            case 'boolean':
                return 'Filters\BooleanFilterType::class';
            case 'datetime':
            case 'vardatetime':
            case 'datetimetz':
                return 'Filters\DateTimeFilterType::class';
            case 'date':
                return 'Filters\DateFilterType::class';
            case 'decimal':
            case 'float':
            case 'integer':
            case 'bigint':
            case 'smallint':
                return 'Filters\NumberFilterType::class';
            case 'string':
            case 'text':
            case 'time':
                return 'Filters\TextFilterType::class';
            case 'entity':
            case 'collection':
                return 'Filters\EntityFilterType::class';
            case 'array':
            case 'virtual':
                return false; //array and virtual types are not yet implemented
            default:
                return false;
        }
    }

    private function getAssociatedFields(ClassMetadataInfo $metadata)
    {
        $fields = array();

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            if ($relation['type'] == ClassMetadataInfo::MANY_TO_ONE ||
                    $relation['type'] == ClassMetadataInfo::ONE_TO_MANY ||
                    $relation['type'] == ClassMetadataInfo::ONE_TO_ONE ||
                    $relation['type'] == ClassMetadataInfo::MANY_TO_MANY) {
                $fields[$fieldName]['name'] = $fieldName;
                $fields[$fieldName]['widget'] = 'Filters\EntityFilterType::class';
                $fields[$fieldName]['class'] = $relation['targetEntity'];
                $fields[$fieldName]['choice_label'] = $this->metadataGuesser->guessChoiceLabelFromClass($relation['targetEntity']);
            }
        }
        return $fields;
    }

}
