<?php

namespace Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;

use PHPCR\RepositoryException;

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
     * {@inheritDoc}
     *
     * This will return any document found at the url or up the path to the
     * prefix. If any of the documents does not extend the symfony Route
     * object, it is filtered out. In the extreme case this can also lead to an
     * empty list being returned.
     */
    public function getRouteCollectionForRequest(Request $request)
    {
        try {
            $collection = parent::getRouteCollectionForRequest($request);
        } catch (RepositoryException $e) {
            // TODO: how to determine whether this is a relevant exception or not?
            // for example, getting /my//test (note the double /) is just an invalid path
            // and means another router might handle this.
            // but if the PHPCR backend is down for example, we want to alert the user

            $collection = new RouteCollection();
        }

        return $collection;
    }

    /**
     * Let the provider use its own method
     */
    public function getRoutesFromCandidates($candidates)
    {
        return $this->getRoutesByNames($candidates);
    }
}
