<?php

namespace Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm;

use Symfony\Cmf\Component\Routing\RedirectRouteInterface;

/**
 * ORM redirect route
 * @author matteo caberlotto mcaber@gmail.com
 */
class RedirectRoute implements RedirectRouteInterface
{
	protected $id;

	protected $uri;

	protected $routeName;

	protected $permanent;

	protected $parameters;

	public function getId()
	{
		return $this->id;
	}

    /**
     * Set whether this redirection should be permanent or not. Default is
     * false.
     *
     * @param boolean $permanent if true this is a permanent redirection
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;
    }

    /**
     * {@inheritDoc}
     */
    public function isPermanent()
    {
        return $this->permanent;
    }

    /**
     * Set the parameters for building this route. Used with both route name
     * and target route document.
     *
     * @param array $parameters a hashmap of key to value mapping for route
     *      parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set the absolute redirection target URI.
     *
     * @param string $uri the absolute URI
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * {@inheritDoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set a symfony route name for this redirection.
     *
     * @param string $routeName
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    public function getRouteTarget()
    {
    	return null;
    }

    public function getContent()
    {
    	return null;
    }

    public function getRouteKey()
    {
    	return $this->getId();
    }
}
