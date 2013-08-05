<?php

namespace Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm;

use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;

/**
 * This enhancer sets the content to target field if the route provides content
 *
 * Only works with RouteObjectInterface routes that can return a referenced
 * content.
 *
 * @author Matteo Caberlotto
 */
class RouteContentEnhancer implements RouteEnhancerInterface
{
    /**
     * @var string field for the route class
     */
    protected $routefield;

    /**
     * @var string field to write hashmap lookup result into
     */
    protected $target;

    /**
     * @param string $routefield        the field name of the route class
     * @param string $target            the field name to set from the map
     * @param array  $contentRepository the content repository used to retrieve objects
     */
    public function __construct($routefield, $target, $contentRepository)
    {
        $this->routefield = $routefield;
        $this->target = $target;
        $this->repository = $contentRepository;
    }

    /**
     * If the route has a content, retrieve and bind to defaults.
     *
     * {@inheritDoc}
     */
    public function enhance(array $defaults, Request $request)
    {
        if (isset($defaults[$this->target])) {
            // no need to do anything
            return $defaults;
        }

        if (! isset($defaults[$this->routefield])) {
            // we can't determine the content
            return $defaults;
        }

        if (isset($defaults[$this->routefield])) {
            $targetObject = $this->repository->findById($defaults[$this->routefield]);
            if (!$targetObject) {
                // object not found
                return $defaults;
            }

            $defaults[$this->target] = $targetObject;
        }

        return $defaults;
    }
}
