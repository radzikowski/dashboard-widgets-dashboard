<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Newscoop\Widgets\Controller\ControllerResolver;
use Newscoop\Widgets\Container\ContainerFactory;
use Newscoop\Widgets\WidgetReader;

$containerFactory = new ContainerFactory(__DIR__, __DIR__.'/app/cache', __DIR__ . '/widgets');
$container = $containerFactory->getContainer();
$widgetReader = new WidgetReader($container->getParameter('dashboard.widgets_dir'));

$routingLoader = $container->get('dashboard.routing.yml_loader');
$widgetReader->registerRoutings($routingLoader, $containerFactory);
$router = $container->get('dashboard.router');

$request = Request::createFromGlobals();
$context = new RequestContext();
$context->fromRequest($request);
$router->setContext($context);
$matcher = new UrlMatcher($router->getRouteCollection(), $context);

$dispatcher = $container->get('dispatcher');
$dispatcher->addSubscriber(new RouterListener($matcher));

$kernel = new HttpKernel($dispatcher, new ControllerResolver($container));
$kernel->handle($request)->send();