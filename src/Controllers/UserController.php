<?php

declare(strict_types=1);

namespace Controllers;

use Classes\Request;
use Classes\Response;
use Classes\User\User;
use Classes\User\UserEntity;
use Exception;
use Services\ValidationService;

class UserController
{
    private User $user;
    private Response $response;
    private Request $request;
    private ValidationService $validation_service;
    private UserEntity $user_entity;

    public function __construct(
        User $user,
        Response $response,
        Request $request,
        ValidationService $validation_service,
        UserEntity $user_entity
    ) {
        $this->user = $user;
        $this->response = $response;
        $this->request = $request;
        $this->validation_service = $validation_service;
        $this->user_entity = $user_entity;
    }

    /**
     * @return void
     */
    public function getAll(): void
    {
        $request_method = $this->request->getRequestMethod();
        if ($request_method !== 'GET' && $request_method !== 'POST') {
            $this->response->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method not allowed. Only allowed methods: GET, POST.'
            );
            return;
        }

        try {
            $users = $this->user->getAll();
            $this->response->sendResponse(
                Response::SUCCESS_STATUS_CODE,
                'Users retrieved successfully.',
                $users
            );
        } catch (Exception $exception) {
            $this->response->sendResponse(Response::INTERNAL_SERVER_ERROR_STATUS_CODE, $exception->getMessage());
        }
    }

    /**
     * @return void
     */
    public function get(): void
    {
        if ($this->request->getRequestMethod() !== 'GET') {
            $this->response->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method not allowed. Only allowed method: GET.'
            );
            return;
        }

        $query_parameters = $this->request->getQueryParameters();
        if (!$this->validateUserId($query_parameters)) {
            return;
        }

        try {
            $user_id = (int) $this->request->getParameter('user_id');
            $user = $this->user->getById($user_id);

            if (empty($user)) {
                $this->response->sendResponse(Response::NOT_FOUND_STATUS_CODE, 'User not found.');
                return;
            }

            $this->response->sendResponse(Response::SUCCESS_STATUS_CODE, 'User retrieved with success.', $user);
        } catch (Exception $exception) {
            $this->response->sendResponse(Response::INTERNAL_SERVER_ERROR_STATUS_CODE, $exception->getMessage());
        }
    }

    /**
     * @return void
     */
    public function create(): void
    {
        if ($this->request->getRequestMethod() !== 'POST') {
            $this->response->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method not allowed. Only allowed method: POST.'
            );
            return;
        }

        try {
            $post_parameters = $this->request->getPostParameters();
            $user_entity = $this->getUserEntity($post_parameters);

            if (!$user_entity) {
                return;
            }

            $result = $this->user->create($user_entity);
            if (empty($result)) {
                $this->response->sendResponse(
                    Response::INTERNAL_SERVER_ERROR_STATUS_CODE,
                    'User could not be created.'
                );
                return;
            }

            $this->response->sendResponse(Response::SUCCESS_STATUS_CODE, 'User created with success.', $result);
        } catch (Exception $exception) {
            $this->response->sendResponse(Response::INTERNAL_SERVER_ERROR_STATUS_CODE, $exception->getMessage());
        }
    }

    /**
     * @return void
     */
    public function update(): void
    {
        if ($this->request->getRequestMethod() !== 'PUT') {
            $this->response->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method not allowed. Only allowed method: PUT.'
            );
            return;
        }

        $post_parameters = $this->request->getPostParameters();
        if (!$this->validateUserId($post_parameters)) {
            return;
        }

        try {
            $user_entity = $this->getUserEntity($post_parameters);
            if (!$user_entity) {
                return;
            }

            $user_id = (int) $this->request->getParameter('user_id');
            $result = $this->user->update($user_id, $user_entity);
            if (empty($result)) {
                $this->response->sendResponse(
                    Response::INTERNAL_SERVER_ERROR_STATUS_CODE,
                    'User could not be updated.'
                );
                return;
            }

            $this->response->sendResponse(Response::SUCCESS_STATUS_CODE, 'User updated with success.', $result);
        } catch (Exception $exception) {
            $this->response->sendResponse(Response::INTERNAL_SERVER_ERROR_STATUS_CODE, $exception->getMessage());
        }
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        if ($this->request->getRequestMethod() !== 'POST') {
            $this->response->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method not allowed. Only allowed method: POST.'
            );
            return;
        }

        $post_parameters = $this->request->getPostParameters();
        if (!$this->validateUserId($post_parameters)) {
            return;
        }

        try {
            $user_id = (int) $this->request->getParameter('user_id');
            $result = $this->user->delete($user_id);

            if (!$result) {
                $this->response->sendResponse(
                    Response::INTERNAL_SERVER_ERROR_STATUS_CODE,
                    'User could not be removed.'
                );
                return;
            }

            $this->response->sendResponse(Response::SUCCESS_STATUS_CODE, 'User deleted with success.');
        } catch (Exception $exception) {
            $this->response->sendResponse(Response::INTERNAL_SERVER_ERROR_STATUS_CODE, $exception->getMessage());
        }
    }

    /**
     * @param array $request_parameters
     * @return bool
     */
    private function validateUserId(array $request_parameters): bool
    {
        if (!isset($request_parameters['user_id'])) {
            $this->response->sendResponse(Response::CLIENT_ERROR_STATUS_CODE, 'user_id is required.');
            return false;
        }

        if (!$this->validation_service->isPositiveInt((int) $request_parameters['user_id'])) {
            $this->response->sendResponse(Response::CLIENT_ERROR_STATUS_CODE, 'user_id is invalid.');
            return false;
        }

        return true;
    }

    /**
     * @param array $post_parameters
     * @return UserEntity|null
     */
    private function getUserEntity(array $post_parameters): ?UserEntity
    {
        $user_entity = $this->user_entity->populateFromArray($post_parameters);
        $validation_errors = $user_entity->validate();

        if (count($validation_errors)) {
            $this->response->sendResponse(
                Response::CLIENT_ERROR_STATUS_CODE,
                'Validation errors.',
                $validation_errors
            );
            return null;
        }

        return $user_entity;
    }
}
