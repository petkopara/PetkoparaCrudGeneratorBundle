<?php

namespace Petkopara\CrudGeneratorBundle\Configuration;

/**
 * Description of ConfigurationBuilder
 *
 * @author Petko Petkov <petkopara@gmail.com>
 */
class ConfigurationBuilder implements ConfigurationBuilderInterface
{
    /*
     * @var Configuration configuration
     */

    private $configuration;

    public function __construct()
    {
        $this->configuration = new Configuration();
    }

    /**
     * 
     * @param type $baseTemplate
     * @return ConfigurationBuilder
     */
    public function setBaseTemplate($baseTemplate)
    {
        $this->configuration->setBaseTemplate($baseTemplate);
        return $this;
    }

    /**
     * 
     * @param type $bundleViews
     * @return $this
     */
    public function setBundleViews($bundleViews)
    {
        $this->configuration->setBundleViews($bundleViews);
        return $this;
    }

    /**
     * 
     * @param type $filterType
     * @return $this
     */
    public function setFilterType($filterType)
    {
        $this->configuration->setFilterType($filterType);
        return $this;
    }

    /**
     * 
     * @param type $withoutBulk
     * @return $this
     */
    public function setWithoutBulk($withoutBulk)
    {
        $this->configuration->setWithoutBulk($withoutBulk);
        return $this;
    }

    /**
     * 
     * @param type $withoutShow
     * @return $this
     */
    public function setWithoutShow($withoutShow)
    {
        $this->configuration->setWithoutShow($withoutShow);
        return $this;
    }

    /**
     * 
     * @param type $withoutWrite
     * @return $this
     */
    public function setWithoutWrite($withoutWrite)
    {
        $this->configuration->setWithoutWrite($withoutWrite);
        return $this;
    }

    public function setFormat($format)
    {
        $this->configuration->setFormat($format);
        return $this;
    }

    public function setOverwrite($overwrite)
    {
        $this->configuration->setOverwrite($overwrite);
        return $this;
    }

    public function setRoutePrefix($routePrefix)
    {
        $this->configuration->setRoutePrefix($routePrefix);
        return $this;
    }

    public function setWithoutPageSize($withoutPageSize)
    {
        $this->configuration->setWithoutPageSize($withoutPageSize);
        return $this;
    }

    public function setWithoutSorting($withoutSorting)
    {
        $this->configuration->setWithoutSorting($withoutSorting);
        return $this;
    }

    /**
     * 
     * @return Configuration 
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

}
