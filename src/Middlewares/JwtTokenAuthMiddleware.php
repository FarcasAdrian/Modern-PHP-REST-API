<?php

declare(strict_types=1);

namespace Middlewares;

use Classes\Request;
use Enums\HttpStatusCodeEnum;
use Interfaces\AuthenticationServiceInterface;
use Interfaces\AuthMiddlewareInterface;
use Interfaces\ResponseInterface;
use Services\ValidationService;
use stdClass;

class JwtTokenAuthMiddleware implements AuthMiddlewareInterface
{
    public function __construct(
        private AuthenticationServiceInterface $authenticationService,
        private ResponseInterface $response,
        private ValidationService $validationService
    ) {}

    /**
     * @param Request $request
     * @return \stdClass|null
     */
    public function handle(Request $request): ?stdClass
    {
        $auth_header = $request->getHeader()->get('Authorization');
        $matches = [];

        if (!$this->validationService->isValidAuthHeader($auth_header, $matches)) {
            $this->response->sendResponse(HttpStatusCodeEnum::UNAUTHORIZED_STATUS_CODE->value, 'Not authorized.');
            exit;
        }

        return $this->authenticationService->authenticate($matches);
    }
}
