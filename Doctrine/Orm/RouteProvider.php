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

    protected function findByName($name)
    {
        return $this->getRoutesRepository()->findOneBy(array('name' => $name));
    }

    protected function findManyByName($names)
    {
        return $this->getRoutesRepository()->findBy(array('name' => $names), array('position' => 'ASC'));
    }

    protected function findManyByStaticPrefix($candidates)
    {
        return $this->getRoutesRepository()->findBy(array('staticPrefix' => $candidates), array('position' => 'ASC'));
    }

    public function getRoutesByStaticPrefix($prefix)
    {
        return $this->findManyByStaticPrefix($prefix);
    }

    protected function getRoutesRepository()
    {
        return $this->getObjectManager()->getRepository($this->className);
    }

    /**
     * {@inheritDoc}
     *
     * This will return any entity found at the url or up the path to the
     * prefix. In the extreme case this can also lead to an
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

        try {
            $routes = $this->findManyByStaticPrefix($candidates);

            foreach ($routes as $key => $route) {
                if (preg_match('/.+\.([a-z]+)$/i', $url, $matches)) {
                    if ($route->getDefault('_format') === $matches[1]) {
                        continue;
                    }

                    $route->setDefault('_format', $matches[1]);
                }
                $collection->add($key, $route);
            }
        } catch (DoctrineException $e) {
            // TODO: how to determine whether this is a relevant exception or not?
            // for example, getting /my//test (note the double /) is just an invalid path
            // and means another router might handle this.
            // but if the PHPCR backend is down for example, we want to alert the user
        }

        return $collection;
    }
}
