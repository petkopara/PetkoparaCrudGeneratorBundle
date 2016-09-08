<?php

namespace Petkopara\TritonCrudBundle\Configuration;

class GeneratorAdvancedConfiguration {

    protected $baseTemplate;
    protected $bundleViews;
    protected $withFilter;
    protected $withBulk;

    function __construct($baseTemplate = 'PetkoparaTritonCrudBundle::base.html.twig', $bundleViews = false, $withFilter = true, $withBulk = true, $needWriteAction = true) {
        $this->baseTemplate = $baseTemplate;
        $this->bundleViews = $bundleViews;
        $this->withFilter = $withFilter;
        $this->withBulk = $needWriteAction ? $withBulk : false;
    }

    public function getBaseTemplate() {
        return $this->baseTemplate;
    }

    public function getBundleViews() {
        return $this->bundleViews;
    }

    public function getWithFilter() {
        return $this->withFilter;
    }

    public function getWithBulk() {
        return $this->withBulk;
    }

    public function setBaseTemplate($baseTemplate) {
        $this->baseTemplate = $baseTemplate;
        return $this;
    }

    public function setBundleViews($bundleViews) {
        $this->bundleViews = $bundleViews;
        return $this;
    }

    public function setWithFilter($withFilter) {
        $this->withFilter = $withFilter;
        return $this;
    }

    public function setWithBulk($withBulk) {
        $this->withBulk = $withBulk;
        return $this;
    }

}
