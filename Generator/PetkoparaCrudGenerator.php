<?php

namespace Petkopara\CrudGeneratorBundle\Generator;

use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Petkopara\CrudGeneratorBundle\Configuration\GeneratorAdvancedConfiguration;
use RuntimeException;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class PetkoparaCrudGenerator extends DoctrineCrudGenerator {

    protected $formFilterGenerator;
    protected $advancedConfig;

    /**
     * Same as Doctrine generate method except the view folders name are camelCase
     * @param BundleInterface $bundle
     * @param string $entity
     * @param ClassMetadataInfo $metadata
     * @param string $format
     * @param string $routePrefix
     * @param boolean $withoutWrite
     * @param boolean $forceOverwrite
     * @param GeneratorAdvancedConfiguration $advancedConfig
     * @throws RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $withoutWrite, $forceOverwrite, GeneratorAdvancedConfiguration $advancedConfig = null) {
        $this->advancedConfig = $advancedConfig;
        $this->routePrefix = $routePrefix;
        $this->routeNamePrefix = self::getRouteNamePrefix($routePrefix);
        $this->actions = $advancedConfig->getCrudActions();

        if (count($metadata->identifier) != 1) {
            throw new RuntimeException('The CRUD generator does not support entity classes with multiple or no primary keys.');
        }

        $this->entity = $entity;
        $this->entitySingularized = lcfirst(Inflector::singularize($entity));
        $this->entityPluralized = lcfirst(Inflector::pluralize($entity));
        $this->bundle = $bundle;
        $this->metadata = $metadata;

        $this->setFormat($format);


        //define where to save the view files
        if (!$advancedConfig->getBundleViews()) { //save in root Resources
            $dir = sprintf('%s/Resources/views/%s', $this->rootDir, str_replace('\\', '/', strtolower($this->entity)));
        } else { //save in bundle Resources
            $dir = sprintf('%s/Resources/views/%s', $bundle->getPath(), str_replace('\\', '/', $this->entity));
        }
        
        $this->generateControllerClass($forceOverwrite);

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
            'base_template' => $this->advancedConfig->getBaseTemplate(),
            'without_bulk_action' => $this->advancedConfig->getWithoutBulk(),
            'filter_type' => $this->advancedConfig->getFilterType(),
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
            'base_template' => $this->advancedConfig->getBaseTemplate(),
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
            'base_template' => $this->advancedConfig->getBaseTemplate(),
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
            'base_template' => $this->advancedConfig->getBaseTemplate(),
        ));
    }

    /**
     * Generates the controller class only.
     * @param boolean $forceOverwrite
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
            'bundle_views' => $this->advancedConfig->getBundleViews(),
            'filter_type' => $this->advancedConfig->getFilterTYpe(),
        ));
    }

}
