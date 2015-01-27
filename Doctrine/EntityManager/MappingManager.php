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
    public function findMainMapping($itemSlug, $sourceSlug, $otherSourceSlug)
    {
        $qb = $this->repository->createQueryBuilder('m');

        $qb->innerJoin('m.left', 'ml');
        $qb->innerJoin('m.right', 'mr');

        $qb->innerJoin('ml.source', 'mls');
        $qb->innerJoin('mr.source', 'mrs');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->eq('ml.slug', ':item_slug'),
                    $qb->expr()->eq('mls.slug', ':source_slug'),
                    $qb->expr()->eq('mrs.slug', ':other_source_slug')
                ),
                $qb->expr()->andX(
                    $qb->expr()->eq('mr.slug', ':item_slug'),
                    $qb->expr()->eq('mrs.slug', ':source_slug'),
                    $qb->expr()->eq('mls.slug', ':other_source_slug')
                )
            )
        );

        $qb->setParameter('item_slug', $itemSlug);
        $qb->setParameter('source_slug', $sourceSlug);
        $qb->setParameter('other_source_slug', $otherSourceSlug);

        $qb->andWhere($qb->expr()->eq('m.main', true));

        $qb->select('m');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByMapperItemSlug($itemSlug, $sourceSlug, $otherSourceSlug)
    {
        $qb = $this->repository->createQueryBuilder('m');

        $qb->innerJoin('m.left', 'ml');
        $qb->innerJoin('m.right', 'mr');

        $qb->innerJoin('ml.source', 'mls');
        $qb->innerJoin('mr.source', 'mrs');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->eq('ml.slug', ':item_slug'),
                    $qb->expr()->eq('mls.slug', ':source_slug'),
                    $qb->expr()->eq('mrs.slug', ':other_source_slug')
                ),
                $qb->expr()->andX(
                    $qb->expr()->eq('mr.slug', ':item_slug'),
                    $qb->expr()->eq('mrs.slug', ':source_slug'),
                    $qb->expr()->eq('mls.slug', ':other_source_slug')
                )
            )
        );

        $qb->setParameter('item_slug', $itemSlug);
        $qb->setParameter('source_slug', $sourceSlug);
        $qb->setParameter('other_source_slug', $otherSourceSlug);

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByMapperItemSlugs(array $itemSlugs, $sourceSlug, $otherSourceSlug)
    {
        $qb = $this->repository->createQueryBuilder('m');

        $qb->innerJoin('m.left', 'ml');
        $qb->innerJoin('m.right', 'mr');

        $qb->innerJoin('ml.source', 'mls');
        $qb->innerJoin('mr.source', 'mrs');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->in('ml.slug', ':item_slugs'),
                    $qb->expr()->eq('mls.slug', ':source_slug'),
                    $qb->expr()->eq('mrs.slug', ':other_source_slug')
                ),
                $qb->expr()->andX(
                    $qb->expr()->in('mr.slug', ':item_slugs'),
                    $qb->expr()->eq('mrs.slug', ':source_slug'),
                    $qb->expr()->eq('mls.slug', ':other_source_slug')
                )
            )
        );

        $qb->setParameter('item_slugs', $itemSlugs);
        $qb->setParameter('source_slug', $sourceSlug);
        $qb->setParameter('other_source_slug', $otherSourceSlug);

        return $qb->getQuery()->getResult();
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
}
