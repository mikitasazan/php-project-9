<?php

declare(strict_types=1);

use App\Database\ConnectionFactory;
use App\Http\PageInspector;
use App\Repository\SiteCheckRepository;
use App\Repository\SiteRepository;
use DI\Container;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Slim\Flash\Messages;
use Slim\Views\PhpRenderer;

/**
 * Wires the container and returns a ready-to-run Slim app. Split out of
 * public/index.php so a test bootstrap can build the same app without
 * going through the webserver.
 */
function buildApp(): \Slim\App
{
    $container = new Container();

    $container->set(PDO::class, ConnectionFactory::fromEnvironment(...));
    $container->set(SiteRepository::class, static fn(ContainerInterface $c) => new SiteRepository($c->get(PDO::class)));
    $container->set(
        SiteCheckRepository::class,
        static fn(ContainerInterface $c) => new SiteCheckRepository($c->get(PDO::class)),
    );
    $container->set(PageInspector::class, static fn() => new PageInspector(new Client()));
    $container->set('flash', static fn() => new Messages());
    $container->set('view', static function () {
        $renderer = new PhpRenderer(dirname(__DIR__) . '/templates');
        $renderer->setLayout('layout.php');

        return $renderer;
    });

    AppFactory::setContainer($container);
    $app = AppFactory::create();
    $container->set('routeParser', static fn() => $app->getRouteCollector()->getRouteParser());

    $isDebug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $app->addErrorMiddleware($isDebug, true, true);

    (require __DIR__ . '/routes.php')($app);

    return $app;
}
