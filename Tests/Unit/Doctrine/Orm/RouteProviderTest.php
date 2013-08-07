<?php

namespace Symfony\Cmf\Bundle\RoutingBundle\Tests\Doctrine\Orm;

use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm\RouteProvider;

class RouteProviderTest extends \PHPUnit_Framework_Testcase
{
    private $route;
    private $route2;
    private $objectManager;
    private $objectManager2;
    private $managerRegistry;
    private $repository;

    public function setUp()
    {
        $this->route = $this->getMockBuilder('Symfony\Component\Routing\Route')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->route2 = $this->getMockBuilder('Symfony\Component\Routing\Route')
            ->disableOriginalConstructor()
            ->getMock();
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->managerRegistry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
    }

    public function testGetRouteCollectionForRequest()
    {
        $this->markTestIncomplete();
    }

    public function testGetRouteByName()
    {
        $this->route
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/test-route'));

        $this->repository
            ->expects($this->any())
            ->method('findOneBy')
            ->will($this->returnValue($this->route))
        ;

        $this->objectManager
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repository))
        ;

        $this->managerRegistry
            ->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($this->objectManager))
        ;

        $routeProvider = new RouteProvider($this->managerRegistry, 'Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Orm\Route');
        $routeProvider->setManagerName('default');

        $foundRoute = $routeProvider->getRouteByName('/test-route');

        $this->assertInstanceOf('Symfony\Component\Routing\Route', $foundRoute);
        $this->assertEquals('/test-route', $foundRoute->getPath());
    }

    public function testGetRoutesByNames()
    {
        $this->route
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/test-route'));

        $this->route2
            ->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/other-route'));

        $this->repository
            ->expects($this->any())
            ->method('findBy')
            ->will($this->returnValue(array($this->route, $this->route2)))
        ;

        $this->objectManager
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repository))
        ;

        $this->managerRegistry
            ->expects($this->any())
            ->method('getManager')
            ->will($this->returnValue($this->objectManager))
        ;

        $routeProvider = new RouteProvider($this->managerRegistry);
        $routeProvider->setManagerName('default');

        $expected = array('/test-route', '/other-route');

        $foundRoutes = $routeProvider->getRoutesFromCandidates($expected);

        foreach ($foundRoutes as $route) {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
            $this->assertContains($route->getPath(), $expected);
        }
    }
}
