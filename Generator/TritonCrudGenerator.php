<?php

namespace Triton\Bundle\CrudBundle\Generator;

use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Exception;
use RuntimeException;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class TritonCrudGenerator extends DoctrineCrudGenerator {

    protected $formFilterGenerator;
    protected $baseTemplate;
    protected $bundleViews;
    protected $withFilter;
    protected $withBulkDelete;

    /**
     * Same as Doctrine generate method except the view folders name are camelCase
     * @param BundleInterface $bundle
     * @param type $entity
     * @param ClassMetadataInfo $metadata
     * @param type $format
     * @param type $routePrefix
     * @param type $needWriteActions
     * @param type $forceOverwrite
     * @param type $baseTemplate
     * @param type $bundleViews
     * @param type $withFilter
     * @param type $withBulkDelete
     * @throws RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite, $baseTemplate = null, $bundleViews = false, $withFilter = true, $withBulkDelete = true) {
        $this->routePrefix = $routePrefix;
        $this->routeNamePrefix = self::getRouteNamePrefix($routePrefix);
        $this->withBulkDelete = $needWriteActions ? $withBulkDelete : false;
        $this->actions = $needWriteActions ? array('index', 'show', 'new', 'edit', 'delete') : array('index', 'show');
        
        if ($needWriteActions && $withBulkDelete) {
            array_push($this->actions, 'bulk');
        }

        if (count($metadata->identifier) != 1) {
            throw new RuntimeException('The CRUD generator does not support entity classes with multiple or no primary keys.');
        }

        $this->entity = $entity;
        $this->entitySingularized = lcfirst(Inflector::singularize($entity));
        $this->entityPluralized = lcfirst(Inflector::pluralize($entity));
        $this->bundle = $bundle;
        $this->metadata = $metadata;
        $this->withFilter = $withFilter;

        $this->setFormat($format);

        if (!empty($baseTemplate)) {
            $this->baseTemplate = $baseTemplate;
        }

        $this->generateControllerClass($forceOverwrite);

        $this->bundleViews = $bundleViews;
        //define where to save the view files
        if (!$this->bundleViews) { //save in root Resources
            $dir = sprintf('%s/Resources/views/%s', $this->rootDir, str_replace('\\', '/', strtolower($this->entity)));
        } else { //save in bundle Resources
            $dir = sprintf('%s/Resources/views/%s', $bundle->getPath(), str_replace('\\', '/', $this->entity));
        }

        if (!file_exists($dir)) {
            $this->filesystem->mkdir($dir, 0777);
        }

        $this->generateIndexView($dir);

        if (in_array('show', $this->actions)) {
            $this->generateShowView($dir);
        }

        if (in_array('new', $this->actions)) {
            $this->generateNewView($dir);
        }

        if (in_array('edit', $this->actions)) {
            $this->generateEditView($dir);
        }

        $this->generateTestClass();
        $this->generateConfiguration();

        if ($this->withFilter) {
            $this->generateFormFilter($bundle, $entity, $metadata, $forceOverwrite);
        }
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
    public function generateFormFilter(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $forceOverwrite) {
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

    public function getFilterType($dbType, $columnName) {
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

    /**
     * Generates the index.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateIndexView($dir) {
        $this->renderFile('crud/views/index.html.twig.twig', $dir . '/index.html.twig', array(
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'entity_pluralized' => $this->entityPluralized,
            'entity_singularized' => $this->entitySingularized,
            'identifier' => $this->metadata->identifier[0],
            'fields' => $this->metadata->fieldMappings,
            'actions' => $this->actions,
            'record_actions' => $this->getRecordActions(),
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'base_template' => $this->baseTemplate,
            'bulk_action' => $this->withBulkDelete,
            'with_filter' => $this->withFilter,
        ));
    }

    /**
     * Generates the show.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateShowView($dir) {
        $this->renderFile('crud/views/show.html.twig.twig', $dir . '/show.html.twig', array(
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'entity_singularized' => $this->entitySingularized,
            'identifier' => $this->metadata->identifier[0],
            'fields' => $this->metadata->fieldMappings,
            'actions' => $this->actions,
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'base_template' => $this->baseTemplate,
        ));
    }

    /**
     * Generates the new.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateNewView($dir) {
        $this->renderFile('crud/views/new.html.twig.twig', $dir . '/new.html.twig', array(
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'entity_singularized' => $this->entitySingularized,
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'actions' => $this->actions,
            'fields' => $this->metadata->fieldMappings,
            'base_template' => $this->baseTemplate,
        ));
    }

    /**
     * Generates the edit.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateEditView($dir) {
        $this->renderFile('crud/views/edit.html.twig.twig', $dir . '/edit.html.twig', array(
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'identifier' => $this->metadata->identifier[0],
            'entity' => $this->entity,
            'entity_singularized' => $this->entitySingularized,
            'fields' => $this->metadata->fieldMappings,
            'bundle' => $this->bundle->getName(),
            'actions' => $this->actions,
            'base_template' => $this->baseTemplate,
        ));
    }

    /**
     * Generates the controller class only.
     */
    protected function generateControllerClass($forceOverwrite) {
        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
                '%s/Controller/%s/%sController.php', $dir, str_replace('\\', '/', $entityNamespace), $entityClass
        );

        if (!$forceOverwrite && file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller as it already exists.');
        }

        $this->renderFile('crud/controller.php.twig', $target, array(
            'actions' => $this->actions,
            'route_prefix' => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle' => $this->bundle->getName(),
            'entity' => $this->entity,
            'entity_singularized' => $this->entitySingularized,
            'entity_pluralized' => $this->entityPluralized,
            'entity_class' => $entityClass,
            'namespace' => $this->bundle->getNamespace(),
            'entity_namespace' => $entityNamespace,
            'format' => $this->format,
            'bundle_views' => $this->bundleViews,
            'with_filter' => $this->withFilter,
        ));
    }

}
