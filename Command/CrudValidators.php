<?php

namespace Petkopara\CrudGeneratorBundle\Command;

/**
 * Petkopara Validator functions.
 */
class CrudValidators
{

    public static function validateFilterType($filterType)
    {
        if (!$filterType) {
            throw new \RuntimeException('Please enter a filter type.');
        }

        $filterType = strtolower($filterType);

        if ($filterType == 1) {//set to default value
            $filterType = CrudGeneratorCommand::FILTER_TYPE_FORM;
        }
        // in case they typed "no", but ok with that
        if ($filterType == 'no' || $filterType == 'n') {
            $filterType = CrudGeneratorCommand::FILTER_TYPE_NONE;
        }

        if (!in_array($filterType, array(CrudGeneratorCommand::FILTER_TYPE_FORM, CrudGeneratorCommand::FILTER_TYPE_INPUT, CrudGeneratorCommand::FILTER_TYPE_NONE))) {
            throw new \RuntimeException(sprintf('Filter type is not supported "%s" is not supported.', $filterType));
        }

        return $filterType;
    }
}
