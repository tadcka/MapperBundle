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
use Tadcka\Mapper\Model\Manager\MappingItemManager as BaseMappingItemManager;
use Tadcka\Mapper\Model\MappingItemInterface;
use Tadcka\Mapper\Model\MappingSourceInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/12/14 6:49 PM
 */
class MappingItemManager extends BaseMappingItemManager
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
    public function findBySlugAndSource($slug, MappingSourceInterface $source)
    {
        return $this->repository->findOneBy(array('slug' => $slug, 'source' => $source));
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlugsAndSource(array $slugs, MappingSourceInterface $source)
    {
        return $this->repository->findBy(array('slug' => $slugs, 'source' => $source));
    }

    /**
     * {@inheritdoc}
     */
    public function findBySource(MappingSourceInterface $source)
    {
        return $this->repository->findBy(array('source' => $source));
    }

    /**
     * {@inheritdoc}
     */
    public function add(MappingItemInterface $item, $save = false)
    {
        $this->em->persist($item);
        if (true === $save) {
            $this->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(MappingItemInterface $item, $save = false)
    {
        $this->em->remove($item);
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
