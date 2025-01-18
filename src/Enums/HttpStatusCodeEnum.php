<?php 

declare(strict_types=1);

namespace Enums;

enum HttpStatusCodeEnum: int {
    case SUCCESS_STATUS_CODE = 200;
    case MISCELLANEOUS_PERSISTENT_WARNING = 299;
    case CLIENT_ERROR_STATUS_CODE = 400;
    case UNAUTHORIZED_STATUS_CODE = 401;
    case NOT_FOUND_STATUS_CODE = 404;
    case METHOD_NOT_ALLOWED_STATUS_CODE = 405;
    case INTERNAL_SERVER_ERROR_STATUS_CODE = 500;
}
