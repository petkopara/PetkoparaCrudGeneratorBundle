<?php

namespace Petkopara\CrudGeneratorBundle\Configuration;

/**
 *
 * @author Petko Petkov <petkopara@gmail.com>
 */
interface ConfigurationBuilderInterface
{

    public function getConfiguration();

    public function setBaseTemplate($baseTemplate);

    public function setBundleViews($bundleViews);

    public function setFilterType($filterType);

    public function setWithoutBulk($withoutBulk);

    public function setWithoutShow($withoutShow);

    public function setWithoutWrite($withoutWrite);

    public function setWithoutSorting($withoutSorting);

    public function setWithoutPageSize($withoutPageSize);

    public function setFormat($format);

    public function setOverwrite($overwrite);

    public function setRoutePrefix($routePrefix);
}
