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
use Tadcka\Bundle\MapperBundle\ModelManager\CategoryManager as BaseCategoryManager;
use Tadcka\Component\Mapper\Model\CategoryInterface;
use Tadcka\Component\Mapper\Model\SourceInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/12/14 6:49 PM
 */
class CategoryManager extends BaseCategoryManager
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
    public function findBySlugAndSource($slug, SourceInterface $source)
    {
        return $this->repository->findOneBy(array('slug' => $slug, 'source' => $source));
    }

    /**
     * {@inheritdoc}
     */
    public function findManyBySlugsAndSource(array $slugs, SourceInterface $source)
    {
        return $this->repository->findBy(array('slug' => $slugs, 'source' => $source));
    }

    /**
     * {@inheritdoc}
     */
    public function findManyBySource(SourceInterface $source)
    {
        return $this->repository->findBy(array('source' => $source));
    }

    /**
     * {@inheritdoc}
     */
    public function add(CategoryInterface $category, $save = false)
    {
        $this->em->persist($category);
        if (true === $save) {
            $this->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(CategoryInterface $category, $save = false)
    {
        $this->em->remove($category);
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
