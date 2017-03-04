<?php

namespace Petkopara\CrudGeneratorBundle\Configuration;

use Petkopara\CrudGeneratorBundle\Command\CrudGeneratorCommand;

class Configuration
{

    protected $baseTemplate = 'PetkoparaCrudGeneratorBundle::base.html.twig';
    protected $bundleViews = false;
    protected $filterType = CrudGeneratorCommand::FILTER_TYPE_INPUT;
    protected $withoutBulk = false;
    protected $withoutShow = false;
    protected $withoutWrite = false;
    protected $withoutSorting = false;
    protected $withoutPageSize = false;
    protected $overwrite = false;
    protected $routePrefix = '';
    protected $format = 'annotation';

    public function getCrudActions()
    {
        $actions = array('index');
        if ($this->withoutWrite === false) {
            $actions = array_merge($actions, array('new', 'edit', 'delete'));

            if ($this->withoutBulk === false) {
                array_push($actions, 'bulk');
            }
        }
        if ($this->withoutShow === false) {
            array_push($actions, 'show');
        }
        return $actions;
    }

    public function getBaseTemplate()
    {
        return $this->baseTemplate;
    }

    public function getBundleViews()
    {
        return $this->bundleViews;
    }

    public function getFilterType()
    {
        return $this->filterType;
    }

    public function setBaseTemplate($baseTemplate)
    {
        $this->baseTemplate = $baseTemplate;
        return $this;
    }

    public function setBundleViews($bundleViews)
    {
        $this->bundleViews = $bundleViews;
        return $this;
    }

    public function setFilterType($filterType)
    {
        $this->filterType = $filterType;
        return $this;
    }

    public function getWithoutBulk()
    {
        return $this->withoutBulk;
    }

    public function getWithoutShow()
    {
        return $this->withoutShow;
    }

    public function setWithoutBulk($withoutBulk)
    {
        if ($this->withoutWrite) {
            $this->withoutBulk = true;
        } else {
            $this->withoutBulk = $withoutBulk;
        }
        return $this;
    }

    public function setWithoutShow($withoutShow)
    {
        $this->withoutShow = $withoutShow;
        return $this;
    }
    
    public function getWithoutWrite()
    {
        return $this->withoutWrite;
    }

    public function getOverwrite()
    {
        return $this->overwrite;
    }

    public function getRoutePrefix()
    {
        return $this->routePrefix;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setWithoutWrite($withoutWrite)
    {
        $this->withoutWrite = $withoutWrite;
    }

    public function setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
    }

    public function setRoutePrefix($routePrefix)
    {
        $this->routePrefix = $routePrefix;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }
    public function getWithoutSorting()
    {
        return $this->withoutSorting;
    }

    public function setWithoutSorting($withoutSorting)
    {
        $this->withoutSorting = $withoutSorting;
        return $this;
    }
    
    
    public function getWithoutPageSize()
    {
        return $this->withoutPageSize;
    }

    public function setWithoutPageSize($withoutPageSize)
    {
        $this->withoutPageSize = $withoutPageSize;
        return $this;
    }






}
