<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Doctrine\EntityManager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Tadcka\Mapper\Model\Manager\MappingManager as BaseMappingManager;
use Tadcka\Mapper\Model\MappingInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/12/14 6:52 PM
 */
class MappingManager extends BaseMappingManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);
        $this->class = $em->getClassMetadata($class)->name;
    }

    /**
     * {@inheritdoc}
     */
    public function findBySourceItemId($itemId, $sourceSlug, $otherSourceSlug)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('m, ml, mr, mls, mrs')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->eq('ml.slug', ':item_id'),
                        $qb->expr()->eq('mls.slug', ':source_slug'),
                        $qb->expr()->eq('mrs.slug', ':other_source_slug')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->eq('mr.slug', ':item_id'),
                        $qb->expr()->eq('mrs.slug', ':source_slug'),
                        $qb->expr()->eq('mls.slug', ':other_source_slug')
                    )
                )
            )
            ->setParameters(
                [
                    'item_id' => $itemId,
                    'source_slug' => $sourceSlug,
                    'other_source_slug' => $otherSourceSlug
                ]
            );

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findBySourceItemIds(array $itemIds, $sourceSlug, $otherSourceSlug)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('m, ml, mr, mls, mrs')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->in('ml.slug', ':item_ids'),
                        $qb->expr()->eq('mls.slug', ':source_slug'),
                        $qb->expr()->eq('mrs.slug', ':other_source_slug')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->in('mr.slug', ':item_ids'),
                        $qb->expr()->eq('mrs.slug', ':source_slug'),
                        $qb->expr()->eq('mls.slug', ':other_source_slug')
                    )
                )
            )
            ->setParameters(
                [
                    'item_ids' => $itemIds,
                    'source_slug' => $sourceSlug,
                    'other_source_slug' => $otherSourceSlug
                ]
            );

        return $qb->getQuery()->getResult();
    }

    public function findItemsBySources($sourceSlug, $otherSourceSlug)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('ml.slug AS left_item, mr.slug AS right_item, mls.slug AS left_source, mrs.slug AS right_source')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->eq('mls.slug', ':source_slug'),
                        $qb->expr()->eq('mrs.slug', ':other_source_slug')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->eq('mrs.slug', ':source_slug'),
                        $qb->expr()->eq('mls.slug', ':other_source_slug')
                    )
                )
            )
            ->setParameters(
                [
                    'source_slug' =>  $sourceSlug,
                    'other_source_slug' =>  $otherSourceSlug
                ]
            );

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findMainBySourceItemId($itemId, $sourceSlug, $otherSourceSlug)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('m')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->eq('ml.slug', ':item_id'),
                        $qb->expr()->eq('mls.slug', ':source_slug'),
                        $qb->expr()->eq('mrs.slug', ':other_source_slug')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->eq('mr.slug', ':item_id'),
                        $qb->expr()->eq('mrs.slug', ':source_slug'),
                        $qb->expr()->eq('mls.slug', ':other_source_slug')
                    )
                )
            )
            ->andWhere($qb->expr()->eq('m.main', $qb->expr()->literal(1)))
            ->setMaxResults(1)
            ->setParameters(
                [
                    'item_id' => $itemId,
                    'source_slug' => $sourceSlug,
                    'other_source_slug' => $otherSourceSlug
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function add(MappingInterface $mapping, $save = false)
    {
        $this->em->persist($mapping);
        if (true === $save) {
            $this->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(MappingInterface $mapping, $save = false)
    {
        $this->em->remove($mapping);
        if (true === $save) {
            $this->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->em->clear($this->getClass());
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder()
    {
        $qb = $this->repository->createQueryBuilder('m');

        $qb
            ->innerJoin('m.leftItem', 'ml')
            ->innerJoin('m.rightItem', 'mr')
            ->innerJoin('ml.source', 'mls')
            ->innerJoin('mr.source', 'mrs');

        return $qb;
    }
}
