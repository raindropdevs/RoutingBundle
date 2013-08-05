<?php

namespace Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr;

use Symfony\Cmf\Component\Routing\RouteProviderInterface;

use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\RouteProvider as BaseRouteProvider;

/**
 * Provide routes loaded from PHPCR-ODM
 *
 * This is <strong>NOT</strong> not a doctrine repository but just the route
 * provider for the NestedMatcher. (you could of course implement this
 * interface in a repository class, if you need that)
 *
 * @author david.buchmann@liip.ch
 */
class RouteProvider extends BaseRouteProvider implements RouteProviderInterface
{
    /**
     * The prefix to add to the url to create the repository path
     *
     * @var string
     */
    protected $idPrefix = '';

    public function setPrefix($prefix)
    {
        $this->idPrefix = $prefix;
    }
}
