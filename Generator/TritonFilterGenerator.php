<?php


namespace Petkopara\TritonCrudBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Exception\RuntimeException;

/**
 * Generates a form Filter class based on a Doctrine entity.
 */
class TritonFilterGenerator extends Generator {

    private $filesystem;
    private $className;
    private $classPath;

    /**
     * Constructor.
     *
     * @param Filesystem $filesystem A Filesystem instance
     */
    public function __construct(Filesystem $filesystem) {
        $this->filesystem = $filesystem;
    }

    public function getClassName() {
        return $this->className;
    }

    public function getClassPath() {
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
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $forceOverwrite) {
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

        $this->renderFile('form/FormFilterType.php.twig', $this->classPath, array(
            'fields_data' => $this->getFieldsDataFromMetadata($metadata),
            'namespace' => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'entity_class' => $entityClass,
            'bundle' => $bundle->getName(),
            'form_class' => $this->className,
            'form_filter_type_name' => strtolower(str_replace('\\', '_', $bundle->getNamespace()) . ($parts ? '_' : '') . implode('_', $parts) . '_' . $this->className),
        ));
    }

    /**
     * Returns an array of fields data (name and filter widget to use).
     * Fields can be both column fields and association fields.
     *
     * @param ClassMetadataInfo $metadata
     * @return array $fields
     */
    private function getFieldsDataFromMetadata(ClassMetadataInfo $metadata) {
        $fieldsData = (array) $metadata->fieldMappings;

        // Convert type to filter widget
        foreach ($fieldsData as $fieldName => $data) {
            $fieldsData[$fieldName]['fieldName'] = $fieldName;
            $fieldsData[$fieldName]['filterWidget'] = $this->getFilterType($fieldsData[$fieldName]['type'], $fieldName);
        }

        return $fieldsData;
    }

    private function getFilterType($dbType, $columnName) {
        switch ($dbType) {
            case 'boolean':
                return 'Filters\BooleanFilterType::class';
            case 'datetime':
            case 'vardatetime':
            case 'datetimetz':
                return 'Filters\DateTimeFilterType::class';
            case 'date':
                return 'Filters\DateFilterType::class';
                break;
            case 'decimal':
            case 'float':
            case 'integer':
            case 'bigint':
            case 'smallint':
                return 'Filters\NumberFilterType::class';
                break;
            case 'string':
            case 'text':
            case 'time':
                return 'Filters\TextFilterType::class';
                break;
            case 'entity':
            case 'collection':
                return 'Filters\EntityFilterType::class';
                break;
            case 'array':
                throw new Exception('The dbType "' . $dbType . '" is only for list implemented (column "' . $columnName . '")');
                break;
            case 'virtual':
                throw new Exception('The dbType "' . $dbType . '" is only for list implemented (column "' . $columnName . '")');
                break;
            default:
                throw new Exception('The dbType "' . $dbType . '" is not yet implemented (column "' . $columnName . '")');
                break;
        }
    }

}
