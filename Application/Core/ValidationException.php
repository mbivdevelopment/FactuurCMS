<?php

declare(strict_types=1);

namespace App\Core;

final class ValidationException extends HttpException
{
    /** @param array<string, string> $errors */
    public function __construct(public readonly array $errors, public readonly Request $request)
    {
        parent::__construct(422, 'De ingevoerde gegevens zijn ongeldig.');
    }

    public function toResponse(): Response
    {
        if ($this->request->wantsJson()) {
            return Response::json(['message' => $this->getMessage(), 'errors' => $this->errors], 422);
        }

        return Response::back()->withErrors($this->errors)->withInput($this->request->all());
    }
}
