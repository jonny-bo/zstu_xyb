<?php

date_default_timezone_set('UTC');

require_once __DIR__ . '/bootstrap.php';

use Topxia\Api\ApiAuth;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

$app = new Silex\Application();

include __DIR__ . '/config/' . API_ENV . '.php';

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
));

$app->before(function (Request $request) use ($app, $biz) {
    $auth = new ApiAuth(include __DIR__ . '/config/whitelist.php', $biz);
    $auth->auth($request);
});

$app->view(function (array $controllerResult) use ($app) {
    return $app->json($controllerResult);
});

$app->error(function (HttpException $exception) use ($app) {

    $error = array(
        'code' => $exception->getStatusCode(),
        'message' => $exception->getMessage(),
    );

    if ($app['debug']) {
        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::create($exception);
        }

        $error['previous'] = array();

        $flags = PHP_VERSION_ID >= 50400 ? ENT_QUOTES | ENT_SUBSTITUTE : ENT_QUOTES;

        $count = count($exception->getAllPrevious());
        $total = $count + 1;
        foreach ($exception->toArray() as $position => $e) {
            $previous = array();

            $ind = $count - $position + 1;

            $previous['message'] = "{$ind}/{$total} {$e['class']}: {$e['message']}";
            $previous['trace'] = array();

            foreach ($e['trace'] as $position => $trace) {
                $content = sprintf('%s. ', $position+1);
                if ($trace['function']) {
                    $content .= sprintf('at %s%s%s(%s)', $trace['class'], $trace['type'], $trace['function'], '...args...');
                }
                if (isset($trace['file']) && isset($trace['line'])) {
                    $content .= sprintf(' in %s line %d', htmlspecialchars($trace['file'], $flags, 'UTF-8'), $trace['line']);
                }

                $previous['trace'][] = $content;
            }

            $error['previous'][] = $previous;
        }
    }

    return array('error' => $error);
});

$app->run();
