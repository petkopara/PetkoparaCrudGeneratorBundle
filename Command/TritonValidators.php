<?php

namespace Petkopara\TritonCrudBundle\Command;

/**
 * Triton Validator functions.
 */
class TritonValidators
{

    public static function validateFilterType($filterType)
    {
        if (!$filterType) {
            throw new \RuntimeException('Please enter a configuration format.');
        }

        $filterType = strtolower($filterType);

        if($filterType==1){//set to default value
            $filterType = 'form';
        }
        // in case they typed "no", but ok with that
        if ($filterType == 'no' || $filterType == 'n') {
            $filterType = 'none';
        }

        if (!in_array($filterType, array('form', 'input', 'none', 'annotation'))) {
            throw new \RuntimeException(sprintf('Filter type is not supported "%s" is not supported.', $filterType));
        }

        return $filterType;
    }
}
