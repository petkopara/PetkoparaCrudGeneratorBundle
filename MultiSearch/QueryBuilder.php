<?php

namespace Petkopara\TritonCrudBundle\MultiSearch;

use Doctrine\ORM\EntityManager;
use Pagerfanta\Pagerfanta;

class QueryBuilder
{

    /**
     * Entity manager
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * Columns to search at
     * @var array
     */
    protected $searchColumns = array();

    /**
     * Entity name
     * @var string
     */
    protected $entityName;

    /**
     * @var string
     */
    protected $request;

    /**
     * @var string
     */
    protected $idName;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param string $className
     */
    public function __construct(
    EntityManager $entityManager, $className)
    {
        $this->entityName = $className;
        $this->entityManager = $entityManager;

        /** @var $metadata \Doctrine\ORM\Mapping\ClassMetadata */
        $metadata = $entityManager->getClassMetadata($className);
        /** @var $reflectionClass \ReflectionClass */
        $reflectionClass = $metadata->getReflectionClass();
        $this->idName = $metadata->getSingleIdentifierFieldName();

        foreach ($reflectionClass->getProperties() as $property) {
            $this->searchColumns[] = $property->getName();
        }
    }

    /**
     * @param string $searchQuery
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createDoctrineQueryBuilder($searchQuery)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $query = $qb
                ->select('entity')
                ->from($this->entityName, 'entity');

        if ($searchQuery == '') {
            return $query;
        }

        $searchQuery = str_replace('*', '%', $searchQuery);
        $searchQueryParts = explode(' ', $searchQuery);

        $subquery = null;
        $subst = 'a';

        foreach ($searchQueryParts as $i => $searchQueryPart) {
            $qbInner = $this->entityManager->createQueryBuilder();

            $paramPosistion = $i + 1;
            ++$subst;

            $whereQuery = $qb->expr()->orX();

            foreach ($this->searchColumns as $column) {
                $whereQuery->add($qb->expr()->like(
                                $subst . '.' . $column, '?' . $paramPosistion
                ));
            }

            $subqueryInner = $qbInner
                    ->select($subst . '.' . $this->idName)
                    ->from($this->entityName, $subst)
                    ->where($whereQuery);

            if ($subquery != null) {
                $subqueryInner->andWhere(
                        $qb->expr()->in(
                                $subst . '.' . $this->idName, $subquery->getQuery()->getDql()
                        )
                );
            }

            $subquery = $subqueryInner;

            $query->setParameter($paramPosistion, $searchQueryPart);
        }

        $query->where(
                $qb->expr()->in(
                        'entity.' . $this->idName, $subquery->getQuery()->getDql()
                )
        );

        return $query;
    }

    /**
     * @param string $searchQuery
     * @return \Pagerfanta\Adapter\DoctrineORMAdapter
     */
    public function getPagerfantaAdapter($searchQuery)
    {
        return new PagerfantaAdapter(
                $this->createDoctrineQueryBuilder($searchQuery), $this->entityManager, $this->entityName
        );
    }

    /**
     * @param string $searchQuery
     * @return \Pagerfanta\Pagerfanta
     */
    public function getPagerfanta($searchQuery)
    {
        return new Pagerfanta($this->getPagerfantaAdapter($searchQuery));
    }

}
