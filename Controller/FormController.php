<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/29/15 9:27 PM
 */
class FormController
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * Constructor.
     *
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }


    public function getAction(Request $request, $item, $source, $otherSource)
    {
        return $this->renderResponse('TadckaMapperBundle:Form:form.html.twig');
    }

    public function postAction(Request $request, $item, $source, $otherSource)
    {
        return new Response();
    }



    /**
     * Render response.
     *
     * @param string $name
     * @param array $parameters
     *
     * @return Response
     */
    private function renderResponse($name, array $parameters = [])
    {
        return new Response($this->templating->render($name, $parameters));
    }
}
