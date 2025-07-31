<?php
declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LimitProductException extends HttpException
{
    public function __construct(
        string $message = 'Product limit reached.',
        int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY
    )
    {
        parent::__construct($statusCode, $message);
    }
}
