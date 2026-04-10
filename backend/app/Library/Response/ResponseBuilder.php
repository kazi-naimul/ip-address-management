<?php

namespace App\Library\Response;

/**
 * @class
 * @summary Represents a Response Format.
 * @name ResponseBuilder
 */
class ResponseBuilder
{
    private array $response;

    /**
     * @var ResponseBuilder|null
     */
    private static ?ResponseBuilder $instance = null;

    /**
     * Create a default response json.
     */
    public function __construct()
    {
        $this->reset();
    }

    public function status(bool $type): self
    {
        $this->response['status'] = $type;

        return $this;
    }

    public function code(int $code): self
    {
        $this->response['code'] = $code;

        return $this;
    }

    public function message(string $message): self
    {
        $this->response['message'] = $message;

        return $this;
    }

    public function data(mixed $data): self
    {
        if ($data !== null) {
            $this->response['data'] = $data;
        }

        return $this;
    }

    public function errors(mixed $errors): self
    {
        $this->response['status'] = false;

        if ($errors !== null) {
            $this->response['errors'] = $errors;
        }

        return $this;
    }

    public function build(): array
    {
        $result = $this->response;
        $this->reset();

        return $result;
    }

    public function reset(): void
    {
        $this->response = [
            'status' => true,
            'code' => 200,
            'message' => 'Successful',
        ];
    }

    public static function getInstance(): self
    {
        return new self();
    }
}
