<?php

namespace Symfony\Cmf\Bundle\RoutingBundle\Doctrine;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

use Symfony\Cmf\Component\Routing\RouteProviderInterface;

use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\DoctrineProvider;

/**
 * Provide routes loaded from PHPCR-ODM
 *
 * This is <strong>NOT</strong> not a doctrine repository but just the route
 * provider for the NestedMatcher. (you could of course implement this
 * interface in a repository class, if you need that)
 *
 * @author david.buchmann@liip.ch
 */
abstract class RouteProvider extends DoctrineProvider implements RouteProviderInterface
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
        $url = $request->getPathInfo();
        $candidates = $this->getCandidates($url);

        $collection = new RouteCollection();

        if (empty($candidates)) {
            return $collection;
        }

        $routes = $this->getRoutesFromCandidates($candidates);
        // filter for valid route objects
        // we can not search for a specific class as PHPCR does not know class inheritance
        // but optionally we could define a node type
        foreach ($routes as $key => $route) {
            if ($route instanceof SymfonyRoute) {
                if (preg_match('/.+\.([a-z]+)$/i', $url, $matches)) {
                    if ($route->getDefault('_format') === $matches[1]) {
                        continue;
                    }

                    $route->setDefault('_format', $matches[1]);
                }
                $collection->add($key, $route);
            }
        }

        return $collection;
    }

    protected function getCandidates($url)
    {
        $candidates = array();
        if ('/' !== $url) {
            if (preg_match('/(.+)\.[a-z]+$/i', $url, $matches)) {
                $candidates[] = $this->idPrefix.$url;
                $url = $matches[1];
            }

            $part = $url;
            while (false !== ($pos = strrpos($part, '/'))) {
                $candidates[] = $this->idPrefix.$part;
                $part = substr($url, 0, $pos);
            }
        }

        $candidates[] = $this->idPrefix;

        return $candidates;
    }

    /**
     * Let the provider use its own method
     */
    abstract protected function getRoutesFromCandidates($candidates);

    /**
     * {@inheritDoc}
     */
    public function getRouteByName($name, $parameters = array())
    {
        // $name is the route document path
        $route = $this->getObjectManager()->find($this->className, $name);
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
        return $this->getObjectManager()->findMany($this->className, $name);
    }

}
