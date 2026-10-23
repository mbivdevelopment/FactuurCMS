<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /** @param array<string, mixed> $data */
    protected function view(string $view, array $data = [], int $status = 200): Response
    {
        return Response::view($view, $data, $status);
    }

    protected function redirect(string $to): Response
    {
        return Response::redirect($to);
    }

    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    /**
     * Valideert de request; bij fouten wordt direct een redirect-back (of JSON 422) gegeven.
     *
     * @param array<string, string> $rules
     * @return array<string, mixed> gevalideerde invoer (alleen de velden uit $rules)
     */
    protected function validate(Request $request, array $rules): array
    {
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new ValidationException($validator->errors(), $request);
        }

        return $request->only(...array_keys($rules));
    }
}
