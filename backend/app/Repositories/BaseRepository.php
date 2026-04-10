<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    protected Model $model;

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        try {
            $data = $this->getSanitizedData($data);

            return $this->model->create($data);
        } catch (\Throwable $e) {

            return null;
        }
    }

    private function getSanitizedData($data)
    {
        if (!isset($this->model)) {
            throw new \Exception('Model not set in repository class.');
        }

        $fillableAttributes = $this->model->getFillable();
        $guardedAttributes = $this->model->getGuarded();

        if (!$fillableAttributes && !$guardedAttributes) {
            return $data;
        }

        return array_filter(
            $data,
            function ($key) use ($fillableAttributes, $guardedAttributes) {
                if (in_array($key, $fillableAttributes)) {
                    return true;
                }
                if (!in_array($key, $guardedAttributes)) {
                    return true;
                }

                return false;
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
