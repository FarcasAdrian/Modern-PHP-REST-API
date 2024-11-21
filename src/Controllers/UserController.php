<?php

namespace Controllers;

use Classes\Request;
use Classes\Response;
use Classes\User;
use Exception;
use HttpResponseException;

class UserController
{
    private User $user;
    private Response $response;
    private Request $request;

    public function __construct(User $user, Response $response, Request $request)
    {
        $this->user = $user;
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * Retrieve all available users.
     * @return void
     * @throws HttpResponseException
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
     * Retrieve a specific user by id.
     * @return void
     * @throws HttpResponseException
     */
    public function get(): void
    {
        if ($this->request->getRequestMethod() !== 'GET') {
            $this->response->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method now allowed. Only allowed method: GET.'
            );
            return;
        }

        $query_parameters = $this->request->getQueryParameters();
        $this->validateUserId($query_parameters);

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
     * Update information for a specific user.
     * @return void
     * @throws HttpResponseException
     */
    public function update(): void
    {
        if ($this->request->getRequestMethod() !== 'POST') {
            $this->response->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method now allowed. Only allowed method: POST.'
            );
            return;
        }

        $post_parameters = $this->request->getPostParameters();
        $this->validateUserId($post_parameters);

        try {
            $user_id = (int) $this->request->getParameter('user_id');
            $result = $this->user->update($user_id, $post_parameters);

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
     * Delete a specific user by id.
     * @return void
     * @throws HttpResponseException
     */
    public function delete(): void
    {
        if ($this->request->getRequestMethod() !== 'POST') {
            $this->response->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method now allowed. Only allowed method: POST.'
            );
            return;
        }

        $post_parameters = $this->request->getPostParameters();
        $this->validateUserId($post_parameters);

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
     * Verify if user id is valid.
     * @param array $request
     * @return bool
     * @throws HttpResponseException
     */
    private function validateUserId(array $request): bool
    {
        if (!isset($request['user_id'])) {
            $this->response->sendResponse(Response::CLIENT_ERROR_STATUS_CODE, 'user_id is required.');
            return false;
        }

        if (!$request['user_id']) {
            $this->response->sendResponse(Response::CLIENT_ERROR_STATUS_CODE, 'user_id is invalid.');
            return false;
        }

        return true;
    }
}
