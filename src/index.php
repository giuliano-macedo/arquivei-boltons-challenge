<?php
require_once 'vendor/autoload.php';
require_once 'core/Database.php';

use Slim\Http\Response as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;

$db = new Database();
$app = AppFactory::create();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(HttpNotFoundException::class, function () use (&$app) {
    $res = $app->getResponseFactory()->createResponse();
    $res->getBody()->write('404');
    return $res->withStatus(404);
});

$app->get('/api/nfe/{accessKey}', function (Request $req, Response $res, array $args) use (&$db) {

    $nfe = null;
    try {
        $nfe = $db->getNfe($args['accessKey']);
    } catch (Exception $e) {
        return $res->withJson(array('status' => 'err', 'msg' => $e->getMessage()), 500);
    }
    if (!$nfe) {
        return $res->withJson(array('status' => 'err', 'msg' => 'Nfe not found'), 404);
    }
    return $res->withJson(array('status' => 'ok', 'data' => $nfe->toNamedArray()));
});

$app->run();
