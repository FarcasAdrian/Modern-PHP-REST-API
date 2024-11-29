<?php

namespace Controllers;

use Classes\Request;
use Classes\Response;
use Services\AuthenticationService;
use Exception;

class AuthenticationController
{
    private AuthenticationService $authentication_service;
    private Response $response;
    private Request $request;

    public function __construct(AuthenticationService $authentication_service, Response $response, Request $request)
    {
        $this->authentication_service = $authentication_service;
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * @return void
     */
    public function login(): void
    {
        if ($this->request->getRequestMethod() !== 'POST') {
            $this->response->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method not allowed. Only allowed method: POST.'
            );
            return;
        }

        $email = $this->request->getParameter('email');
        $password = $this->request->getParameter('password');

        if (empty($email) || empty($password)) {
            $this->response->sendResponse(Response::CLIENT_ERROR_STATUS_CODE, 'Invalid credentials.');
            return;
        }

        try {
            $jw_token = $this->authentication_service->authenticateUser($email, $password);

            if (!$jw_token) {
                return;
            }

            $data = ['token' => $jw_token];
            $this->response->sendResponse(Response::SUCCESS_STATUS_CODE, 'Login successful.', $data);
        } catch (Exception $exception) {
            $this->response->sendResponse(Response::UNAUTHORIZED_STATUS_CODE, $exception->getMessage());
        }
    }

    public function logout(): void
    {
        if ($this->request->getRequestMethod() !== 'POST') {
            $this->response->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method not allowed. Only allowed method: POST.'
            );
            return;
        }

        $this->response->sendResponse(Response::SUCCESS_STATUS_CODE, 'Logout successful.');
    }
}