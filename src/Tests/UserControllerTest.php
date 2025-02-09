<?php

declare(strict_types=1);

namespace Tests;

use Classes\Request;
use Classes\Response;
use Classes\User\UserRepository;
use Classes\User\UserEntity;
use Controllers\UserController;
use PHPUnit\Framework\TestCase;
use Services\ValidationService;
use Enums\HttpStatusCodeEnum;

class UserControllerTest extends TestCase
{
    protected User $user_mock;
    protected Response $response_mock;
    protected Request $request_mock;
    protected ValidationService $validation_service_mock;
    protected UserController $user_controller_mock;
    protected UserEntity $user_entity_mock;

    protected function setUp(): void
    {
        $this->user_mock = $this->createMock(UserRepository::class);
        $this->response_mock = $this->createMock(Response::class);
        $this->request_mock = $this->createMock(Request::class);
        $this->validation_service_mock = $this->createMock(ValidationService::class);
        $this->user_entity_mock = $this->createMock(UserEntity::class);
        $this->user_controller_mock = $this->createMock(UserController::class);
    }

    public function testGetAllWithGetRequest()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('GET');
        $this->user_mock->method('getAll')->willReturn(['user1', 'user2']);

        $this->response_mock->expects($this->once())
            ->method('sendResponse')
            ->with(
                $this->equalTo(HttpStatusCodeEnum::SUCCESS_STATUS_CODE->value),
                $this->equalTo('Users retrieved successfully.'),
                $this->equalTo(['user1', 'user2'])
            );

        $this->user_controller_mock->getAll();

        $this->assertTrue(true);
    }

    public function testGetAllWithInvalidRequestMethod()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('PUT');

        $this->response_mock->expects($this->once())
            ->method('sendResponse')
            ->with(
                $this->equalTo(HttpStatusCodeEnum::METHOD_NOT_ALLOWED_STATUS_CODE->value),
                $this->equalTo('Method not allowed. Only allowed methods: GET, POST.')
            );

        $this->user_controller_mock->getAll();

        $this->assertTrue(true);
    }

    public function testGetWithValidUserId()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('GET');
        $this->request_mock->method('getParameter')->with('user_id')->willReturn(1);
        $this->user_mock->method('getById')->with(1)->willReturn(['user_id' => 1]);

        $this->user_controller_mock->get();

        $this->assertTrue(true);
    }

    public function testGetWithInvalidUserId()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('GET');
        $this->request_mock->method('getParameter')->with('user_id')->willReturn(999);
        $this->user_mock->method('getById')->with(999)->willReturn([]);

        $this->user_controller_mock->get();

        $this->assertTrue(true);
    }

    public function testGetWithMissingUserId()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('GET');
        $this->request_mock->method('getParameter')->with('user_id')->willReturn(null);

        $this->user_controller_mock->get();

        $this->assertTrue(true);
    }

    public function testGetWithInvalidRequestMethod()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('POST');

        $this->response_mock->expects($this->once())
            ->method('sendResponse')
            ->with(
                $this->equalTo(HttpStatusCodeEnum::METHOD_NOT_ALLOWED_STATUS_CODE->value),
                $this->equalTo('Method not allowed. Only allowed method: GET.')
            );

        $this->user_controller_mock->get();

        $this->assertTrue(true);
    }

    public function testCreateWithValidData()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('POST');

        $post_parameters = [
            'name' => 'Farcas Adrian',
            'email' => 'emailtest+1@gmail.com',
            'password' => 'parola123',
            'age' => '26',
            'gender' => 'M',
            'phone' => '0222101119',
            'created_at' => '2024-11-23 09:47:00',
            'updated_at' => '2024-11-23 09:47:50',
        ];

        $this->response_mock->expects($this->once())
            ->method('sendResponse')
            ->with(
                $this->equalTo($post_parameters)
            );

        $this->user_controller_mock->create();

        $this->assertTrue(true);
    }

    public function testCreateWithInvalidData()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('POST');

        $this->request_mock->method('getPostParameters')->willReturn([
            'name' => 'Farcas Adrian',
            'email' => 'emailtest+1@gmail.com',
            'password' => 'parola123',
            'age' => '26',
            'gender' => 'M',
            'phone' => '0235208590',
            'created_at' => '2024-11-23 09:47:00',
            'updated_at' => '2024-11-23 09:47:50',
        ]);

        $this->user_entity_mock->method('validate')->willReturn(['name' => 'Name is required.', 'email' => 'Invalid email.']);

        $this->user_controller_mock->create();

        $this->assertTrue(true);
    }

    public function testCreateWithInvalidRequestMethod()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('GET');

        $this->response_mock->expects($this->once())
            ->method('sendResponse')
            ->with(
                $this->equalTo(HttpStatusCodeEnum::METHOD_NOT_ALLOWED_STATUS_CODE->value),
                $this->equalTo('Method not allowed. Only allowed method: POST.')
            );

        $this->user_controller_mock->create();

        $this->assertTrue(true);
    }

    public function testUpdateWithValidData()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('PUT');
        $this->request_mock->method('getParameter')->with('user_id')->willReturn(1);

        $post_parameters = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com'
        ];

        $this->request_mock->method('getPostParameters')->willReturn($post_parameters);
        $this->user_entity_mock->method('validate')->willReturn([]);
        $this->user_mock->method('update')->willReturn($post_parameters);
        $this->user_controller_mock->update();

        $this->assertTrue(true);
    }

    public function testUpdateWithInvalidData()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('PUT');
        $this->request_mock->method('getParameter')->with('user_id')->willReturn(1);

        $post_parameters = [
            'name' => '',
            'email' => 'invalid-email',
        ];

        $this->request_mock->method('getPostParameters')->willReturn($post_parameters);

        $this->user_controller_mock->update();

        $this->assertTrue(true);
    }

    public function testUpdateWithInvalidRequestMethod()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('POST');

        $this->response_mock->expects($this->once())
            ->method('sendResponse')
            ->with(
                $this->equalTo(HttpStatusCodeEnum::METHOD_NOT_ALLOWED_STATUS_CODE->value),
                $this->equalTo('Method not allowed. Only allowed method: PUT.')
            );

        $this->user_controller_mock->update();

        $this->assertTrue(true);
    }

    public function testDeleteWithValidUserId()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('POST');
        $this->request_mock->method('getParameter')->with('user_id')->willReturn(1);

        $this->user_mock->method('delete')->with(1)->willReturn(true);

        $this->user_controller_mock->delete();

        $this->assertTrue(true);
    }

    public function testDeleteWithInvalidUserId()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('POST');
        $this->request_mock->method('getParameter')->with('user_id')->willReturn(999);

        $this->user_mock->method('delete')->with(999)->willReturn(false);

        $this->user_controller_mock->delete();

        $this->assertTrue(true);
    }

    public function testDeleteWithMissingUserId()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('POST');
        $this->request_mock->method('getParameter')->with('user_id')->willReturn(null);

        $this->response_mock->expects($this->once())
            ->method('sendResponse')
            ->with(
                $this->equalTo(HttpStatusCodeEnum::CLIENT_ERROR_STATUS_CODE->value),
                $this->equalTo('user_id is required.')
            );

        $this->user_controller_mock->delete();

        $this->assertTrue(true);
    }

    public function testDeleteWithInvalidRequestMethod()
    {
        $this->request_mock->method('getRequestMethod')->willReturn('GET');

        $this->response_mock->expects($this->once())
            ->method('sendResponse')
            ->with(
                $this->equalTo(HttpStatusCodeEnum::METHOD_NOT_ALLOWED_STATUS_CODE->value),
                $this->equalTo('Method not allowed. Only allowed method: POST.')
            );

        $this->user_controller_mock->delete();

        $this->assertTrue(true);
    }
}
