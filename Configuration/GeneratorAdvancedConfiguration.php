<?php namespace Petkopara\CrudGeneratorBundle\Configuration;

class GeneratorAdvancedConfiguration
{

    protected $baseTemplate;
    protected $bundleViews;
    protected $filterType;
    protected $withoutBulk;
    protected $withoutShow;
    protected $withoutWrite;

    public function __construct($baseTemplate = 'PetkoparaCrudGeneratorBundle::base.html.twig', $bundleViews = false, $filterType = 'form', $withoutBulk = false, $withoutShow = false, $withoutWrite = false)
    {
        $this->baseTemplate = $baseTemplate;
        $this->bundleViews = $bundleViews;
        $this->filterType = $filterType;
        $this->withoutShow = $withoutShow;
        $this->withoutBulk = $withoutBulk;
        $this->withoutWrite = $withoutWrite;
    }

    public function getCrudActions()
    {
        $actions = array('index');
        if ($this->withoutWrite == false) {
            $actions = array_merge($actions, array('new', 'edit', 'delete'));

            if ($this->withoutBulk == false) {
                array_push($actions, 'bulk');
            }
        }
        if ($this->withoutShow == false) {
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
        $this->withoutBulk = $withoutBulk;
        return $this;
    }

    public function setWithoutShow($withoutShow)
    {
        $this->withoutShow = $withoutShow;
        return $this;
    }
}
