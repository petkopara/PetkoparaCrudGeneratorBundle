<?php
namespace Petkopara\CrudGeneratorBundle\Generator\Guesser;

use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;

/**
 * Description of MetadataGuesser
 *
 * @author Petkov Petkov <petkopara@gmail.com>
 */
class MetadataGuesser
{

    private $metadataFactory;

    /**
     * Constructor.
     *
     * @param DisconnectedMetadataFactory $metadataFactory DisconnectedMetadataFactory instance
     */
    public function __construct(DisconnectedMetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * Trying to find string field in relation entity. 
     * @param type $entity
     * @return string
     */
    public function guessChoiceLabelFromClass($entity)
    {
        $metadata = $this->metadataFactory->getClassMetadata($entity)->getMetadata();
        foreach ($metadata[0]->fieldMappings as $fieldName => $field) {
            if ($field['type'] == 'string') {
                return $fieldName;
            }
        }
        //if no string field found, return id
        return 'id';
    }

}
