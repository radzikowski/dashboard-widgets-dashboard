<?php

require_once __DIR__.'/vendor/autoload.php';

use Newscoop\Widgets\Container\ContainerFactory;
use Newscoop\Widgets\Widget\WidgetReader;

// set dashboard and widgets directory location in parameters
$containerFactory = new ContainerFactory(__DIR__, __DIR__.'/app/cache/',  __DIR__ . '/widgets');
$container = $containerFactory->getContainer();
$widgetReader = new WidgetReader($container->getParameter('dashboard.widgets_dir'));

$view = $container->get('dashboard.view');
$allWidgets = $widgetReader->findAllWidgets();

echo $view->render('Resources/views/widgetsDashboard.html.twig', array('allWidgets' => $allWidgets));