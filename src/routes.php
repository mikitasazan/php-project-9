<?php

declare(strict_types=1);

use App\Domain\SiteCheck;
use App\Http\PageInspector;
use App\Http\PageUnreachableException;
use App\Http\UrlAddress;
use App\Repository\SiteCheckRepository;
use App\Repository\SiteRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Exception\HttpNotFoundException;

/**
 * All named routes for the app, registered onto $app. A closure body
 * runs bound to the container, so `$this->get(...)` reaches any service.
 */
return function (App $app): void {
    $viewData = function (Request $request, array $data = []): array {
        /** @var ContainerInterface $this */
        $route = $request->getAttribute('route');

        return array_merge([
            'flash' => $this->get('flash')->getMessages(),
            'routeParser' => $this->get('routeParser'),
            'currentRouteName' => $route?->getName(),
        ], $data);
    };

    $app->get('/', function (Request $request, Response $response) use ($viewData): Response {
        return $this->get('view')->render($response, 'home.php', $viewData->call($this, $request, [
            'submittedUrl' => '',
        ]));
    })->setName('root');

    $app->get('/urls', function (Request $request, Response $response) use ($viewData): Response {
        /** @var SiteRepository $sites */
        $sites = $this->get(SiteRepository::class);
        /** @var SiteCheckRepository $checks */
        $checks = $this->get(SiteCheckRepository::class);

        return $this->get('view')->render($response, 'urls/index.php', $viewData->call($this, $request, [
            'sites' => $sites->findAll(),
            'latestChecks' => $checks->findLatestPerSite(),
        ]));
    })->setName('urls.index');

    $app->post('/urls', function (Request $request, Response $response) use ($viewData): Response {
        $submitted = (string) ($request->getParsedBody()['url'] ?? '');

        if (!UrlAddress::isValid($submitted)) {
            $this->get('flash')->addMessageNow('error', 'Некорректный URL');

            return $this->get('view')->render($response->withStatus(422), 'home.php', $viewData->call($this, $request, [
                'submittedUrl' => $submitted,
            ]));
        }

        /** @var SiteRepository $sites */
        $sites = $this->get(SiteRepository::class);
        [$site, $wasCreated] = $sites->findOrCreate(UrlAddress::normalize($submitted));

        $message = $wasCreated ? 'Страница успешно добавлена' : 'Страница уже существует';
        $this->get('flash')->addMessage('success', $message);

        $target = $this->get('routeParser')->urlFor('urls.show', ['id' => (string) $site->getId()]);

        return $response->withStatus(302)->withHeader('Location', $target);
    })->setName('urls.store');

    $app->get('/urls/{id:[0-9]+}', function (Request $request, Response $response, array $args) use (
        $viewData,
    ): Response {
        /** @var SiteRepository $sites */
        $sites = $this->get(SiteRepository::class);
        $site = $sites->find((int) $args['id']);

        if ($site === null) {
            throw new HttpNotFoundException($request);
        }

        /** @var SiteCheckRepository $checks */
        $checks = $this->get(SiteCheckRepository::class);

        return $this->get('view')->render($response, 'urls/show.php', $viewData->call($this, $request, [
            'site' => $site,
            'checks' => $checks->findBySiteId($site->getId()),
        ]));
    })->setName('urls.show');

    $app->post('/urls/{id:[0-9]+}/checks', function (Request $request, Response $response, array $args): Response {
        /** @var SiteRepository $sites */
        $sites = $this->get(SiteRepository::class);
        $site = $sites->find((int) $args['id']);

        if ($site === null) {
            throw new HttpNotFoundException($request);
        }

        $target = $this->get('routeParser')->urlFor('urls.show', ['id' => (string) $site->getId()]);

        /** @var PageInspector $inspector */
        $inspector = $this->get(PageInspector::class);

        try {
            $result = $inspector->inspect($site->getName());
        } catch (PageUnreachableException) {
            $this->get('flash')->addMessage('error', 'Произошла ошибка при проверке, не удалось подключиться');

            return $response->withStatus(302)->withHeader('Location', $target);
        }

        /** @var SiteCheckRepository $checks */
        $checks = $this->get(SiteCheckRepository::class);
        $checks->create(new SiteCheck(
            siteId: $site->getId(),
            statusCode: $result->statusCode,
            h1: $result->h1,
            title: $result->title,
            description: $result->description,
        ));

        $this->get('flash')->addMessage('success', 'Страница успешно проверена');

        return $response->withStatus(302)->withHeader('Location', $target);
    })->setName('urls.checks.store');
};
