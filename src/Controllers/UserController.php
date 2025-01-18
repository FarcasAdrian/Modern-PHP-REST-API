<?php

declare(strict_types=1);

namespace Controllers;

use Classes\Request;
use Classes\Response;
use Classes\User\User;
use Classes\User\UserEntity;
use Exception;
use Services\ValidationService;
use Enums\HttpStatusCodeEnum;

class UserController
{
    public function __construct(
        private User $user,
        private Response $response,
        private Request $request,
        private ValidationService $validation_service,
        private UserEntity $user_entity
    ) {}

    /**
     * @return void
     */
    public function getAll(): void
    {
        $request_method = $this->request->getRequestMethod();
        if ($request_method !== 'GET' && $request_method !== 'POST') {
            $this->response->sendResponse(
                HttpStatusCodeEnum::METHOD_NOT_ALLOWED_STATUS_CODE->value,
                'Method not allowed. Only allowed methods: GET, POST.'
            );
            return;
        }

        try {
            $users = $this->user->getAll();
            $this->response->sendResponse(
                HttpStatusCodeEnum::SUCCESS_STATUS_CODE->value,
                'Users retrieved successfully.',
                $users
            );
        } catch (Exception $exception) {
            $this->response->sendResponse(HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value, $exception->getMessage());
        }
    }

    /**
     * @return void
     */
    public function get(): void
    {
        if ($this->request->getRequestMethod() !== 'GET') {
            $this->response->sendResponse(
                HttpStatusCodeEnum::METHOD_NOT_ALLOWED_STATUS_CODE->value,
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
                $this->response->sendResponse(HttpStatusCodeEnum::NOT_FOUND_STATUS_CODE->value, 'User not found.');
                return;
            }

            $this->response->sendResponse(HttpStatusCodeEnum::SUCCESS_STATUS_CODE->value, 'User retrieved with success.', $user);
        } catch (Exception $exception) {
            $this->response->sendResponse(HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value, $exception->getMessage());
        }
    }

    /**
     * @return void
     */
    public function create(): void
    {
        if ($this->request->getRequestMethod() !== 'POST') {
            $this->response->sendResponse(
                HttpStatusCodeEnum::METHOD_NOT_ALLOWED_STATUS_CODE->value,
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
                    HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value,
                    'User could not be created.'
                );
                return;
            }

            $this->response->sendResponse(HttpStatusCodeEnum::SUCCESS_STATUS_CODE->value, 'User created with success.', $result);
        } catch (Exception $exception) {
            $this->response->sendResponse(HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value, $exception->getMessage());
        }
    }

    /**
     * @return void
     */
    public function update(): void
    {
        if ($this->request->getRequestMethod() !== 'PUT') {
            $this->response->sendResponse(
                HttpStatusCodeEnum::METHOD_NOT_ALLOWED_STATUS_CODE->value,
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
                    HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value,
                    'User could not be updated.'
                );
                return;
            }

            $this->response->sendResponse(HttpStatusCodeEnum::SUCCESS_STATUS_CODE->value, 'User updated with success.', $result);
        } catch (Exception $exception) {
            $this->response->sendResponse(HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value, $exception->getMessage());
        }
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        if ($this->request->getRequestMethod() !== 'POST') {
            $this->response->sendResponse(
                HttpStatusCodeEnum::METHOD_NOT_ALLOWED_STATUS_CODE->value,
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
                    HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value,
                    'User could not be removed.'
                );
                return;
            }

            $this->response->sendResponse(HttpStatusCodeEnum::SUCCESS_STATUS_CODE->value, 'User deleted with success.');
        } catch (Exception $exception) {
            $this->response->sendResponse(HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value, $exception->getMessage());
        }
    }

    /**
     * @param array $request_parameters
     * @return bool
     */
    private function validateUserId(array $request_parameters): bool
    {
        if (!isset($request_parameters['user_id'])) {
            $this->response->sendResponse(HttpStatusCodeEnum::CLIENT_ERROR_STATUS_CODE->value, 'user_id is required.');
            return false;
        }

        if (!$this->validation_service->isPositiveInt((int) $request_parameters['user_id'])) {
            $this->response->sendResponse(HttpStatusCodeEnum::CLIENT_ERROR_STATUS_CODE->value, 'user_id is invalid.');
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
                HttpStatusCodeEnum::CLIENT_ERROR_STATUS_CODE->value,
                'Validation errors.',
                $validation_errors
            );
            return null;
        }

        return $user_entity;
    }
}
