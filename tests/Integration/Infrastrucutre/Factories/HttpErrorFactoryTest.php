<?php

declare(strict_types=1);

namespace Tests\Integration;

use Infrastructure\Factories\HttpErrorFactory;
use Infrastructure\Handlers\HttpError;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\ServerRequestCreatorFactory;
use Tests\TestCase;

use function call_user_func;
use function Infrastructure\Adapters\Support\configPath;

/**
 * @covers Infrastructure\Factories\HttpErrorFactory
 */
final class HttpErrorFactoryTest extends TestCase
{
    private HttpErrorFactory $httpErrorFactory;

    private ServerRequestInterface $request;

    /**
     * Test response template when an HttpNotFoundException exception is thrown.
     */
    public function testResponseTemplateWhenAnHttpNotFoundExceptionIsThrown(): void
    {
        $container  = require configPath('container.php');
        /** @var HttpError $httpError */
        $httpError  = call_user_func($this->httpErrorFactory, $container);
        /** @var App */
        $app = $container->get(App::class);
        $middleware = $app
            ->addErrorMiddleware(true, false, false)
            ->setDefaultErrorHandler($httpError);

        /** @var JsonResponse */
        $response = $middleware->handleException($this->request, new HttpNotFoundException($this->request));
        $expected = [
            'statusCode' => 404,
            'error' => [
                'type'          => 'RESOURCE_NOT_FOUND',
                'description'   => 'Not found.',
            ],
        ];

        $this->assertSame($expected, $response->getPayload());
    }

    protected function setUp(): void
    {
        $this->httpErrorFactory = new HttpErrorFactory();
        $this->request          = ServerRequestCreatorFactory::create()->createServerRequestFromGlobals();
    }
}
