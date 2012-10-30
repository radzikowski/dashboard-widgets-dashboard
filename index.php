<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Loader\ClosureLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Newscoop\Widgets\Controller\ControllerResolver;

$app = new Silex\Application();

$app->get('/dashboard', function () use ($app) {
    // set dashboard and widgets directory location in parameters
    $containerFactory = new \Newscoop\Widgets\Container\ContainerFactory();
    $container = $containerFactory->getContainer();

    $view = $container->get('view');
    $widgetReader = new \Newscoop\Widgets\WidgetReader();
    $allWidgets = $widgetReader->findAllWidgets();

    echo $view->render('views/widgetsDashboard.html.twig', array('allWidgets' => $allWidgets));
});

$app->get('/widgets', function () use ($app) {
    $closure = function() {
    $routes = new RouteCollection();
        /**
         * Here we will load routing form widgets (from config/route.yml with YamlLoader)
         */
        $routes->add('route_name', new Route('/helloWorld', array(
            '_controller' => 'Newscoop\HelloWorld\Controller\HelloWorldController::indexAction'
        )));

        return $routes;
    };

    $loader = new ClosureLoader();
    $collection = $loader->load($closure);

    // set dashboard and widgets directory location in parameters
    $containerFactory = new \Newscoop\Widgets\Container\ContainerFactory();
    $container = $containerFactory->getContainer();

    $request = Request::createFromGlobals();
    $context = new RequestContext();
    $context->fromRequest($request);
    $matcher = new UrlMatcher($collection, $context);

    $dispatcher = $container->get('dispatcher');
    $dispatcher->addSubscriber(new RouterListener($matcher));

    $kernel = new HttpKernel($dispatcher, new ControllerResolver($container));
    $kernel->handle($request)->send();
});

$app->run();