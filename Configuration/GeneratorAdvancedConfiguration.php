<?php

namespace Petkopara\TritonCrudBundle\Configuration;

class GeneratorAdvancedConfiguration {

    protected $baseTemplate;
    protected $bundleViews;
    protected $withFilter;
    protected $withBulkDelete;

    function __construct($baseTemplate, $bundleViews, $withFilter, $withBulkDelete, $needWriteAction) {
        $this->baseTemplate = $baseTemplate;
        $this->bundleViews = $bundleViews;
        $this->withFilter = $withFilter;
        $this->withBulkDelete = $needWriteAction ? $withBulkDelete : false;
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

    public function getWithBulkDelete() {
        return $this->withBulkDelete;
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

    public function setWithBulkDelete($withBulkDelete) {
        $this->withBulkDelete = $withBulkDelete;
        return $this;
    }

}
