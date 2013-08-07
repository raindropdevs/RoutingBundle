<?php

namespace Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\RouteProvider as BaseRouteProvider;

use Doctrine\ORM\Exception as DoctrineException;

/**
 * Provider loading routes from Doctrine
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
     * This will return any entity found at the url or up the path to the
     * prefix. In the extreme case this can also lead to an
     * empty list being returned.
     */
    public function getRouteCollectionForRequest(Request $request)
    {
        try {
            $collection = parent::getRouteCollectionForRequest($request);
        } catch (DoctrineException $e) {
            // TODO: how to determine whether this is a relevant exception or not?
            // for example, getting /my//test (note the double /) is just an invalid path
            // and means another router might handle this.
            // but if the PHPCR backend is down for example, we want to alert the user

            $collection = new RouteCollection();
        }

        return $collection;
    }

    /**
     * Retrieve routes from ORM
     */
    public function getRoutesFromCandidates($candidates)
    {
        return $this->getRoutesRepository()->findBy(array('staticPrefix' => $candidates), array('position' => 'ASC'));
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteByName($name, $parameters = array())
    {
        // $name is the route document path
        $route = $this->getRoutesRepository()->findOneBy(array('name' => $name));
        if (!$route) {
            throw new RouteNotFoundException("No route found for path '$name'");
        }

        return $route;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoutesByNames($names, $parameters = array())
    {
        return $this->getRoutesRepository()->findBy(array('name' => $names), array('position' => 'ASC'));
    }

    /**
     * Returns current route repository
     */
    protected function getRoutesRepository()
    {
        return $this->getObjectManager()->getRepository($this->className);
    }
}
