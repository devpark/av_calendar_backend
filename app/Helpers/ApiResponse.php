<?php

namespace App\Helpers;

use Traversable;
use League\Fractal\Manager;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Response helper class providing consistent structure of the json content
 * that is sent by the application. It tries to transform the resources
 * using dedicated transformer if there is one in default namespace.
 *
 * @see \App\Http\Transformers\
 */
class ApiResponse
{
    /**
     * Transformers namespace.
     *
     * @var string
     */
    private static $namespace = 'App\Http\Transformers\\';

    /**
     * Json response to valid request.
     *
     * @param mixed $data
     * @param int $code
     * @param array $additional
     * @param array $headers
     * @param int $options
     *
     * @return JsonResponse
     */
    public static function responseOk(
        $data = [],
        $code = 200,
        array $additional = [],
        array $headers = [],
        $options = 0
    ) {
        $json = array_merge([
            'data' => self::transform($data),
        ], $additional, ['exec_time' => self::getExecutionTime()]);

        return new JsonResponse($json, $code, $headers, $options);
    }

    /**
     * Json response to invalid request.
     *
     * @param string $errorCode
     * @param int $status
     * @param array $fields
     * @param array $headers
     * @param int $options
     *
     * @return JsonResponse
     */
    public static function responseError(
        $errorCode,
        $status,
        $fields = [],
        $headers = [],
        $options = 0
    ) {
        $json = [
            'code' => $errorCode,
            'fields' => $fields,
            'exec_time' => self::getExecutionTime(),
        ];

        return new JsonResponse($json, $status, $headers, $options);
    }

    /**
     * Get API execution time
     *
     * @return float
     */
    protected static function getExecutionTime()
    {
        return defined('LARAVEL_START') ?
            round(microtime(true) - LARAVEL_START, 4) : 0;
    }

    /**
     * Transform resource with fractal transformers if possible.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    private static function transform($data)
    {
        //        if ($data instanceof LengthAwarePaginator) {
//            return json_decode($data->toJson(), true);
//        }

        $fractal = new Manager();

        $transformerUsed = false;

        if (self::isResource($data) &&
            $transformer = self::hasTransformer($data)
        ) {
            $data = $fractal->createData(new Item($data, new $transformer()))
                ->toArray();
            $transformerUsed = true;
        }

        if (self::isCollection($data) && self::isResource($data[0]) &&
            $transformer = self::hasTransformer($data[0])
        ) {
            $data =
                $fractal->createData(new Collection($data, new $transformer()))
                    ->toArray();
            $transformerUsed = true;
        }
        // to transform also nested resources ['users' => [0 => User]]
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::transform($v);
            }
        }

        return $transformerUsed ? Arr::get($data, 'data', $data) : $data;
    }

    private static function hasTransformer($resource)
    {
        return class_exists(
            $transformer = self::$namespace . class_basename($resource))
            ? $transformer
            : false;
    }

    private static function isResource($data)
    {
        return $data instanceof Model;
    }

    private static function isCollection($data)
    {
        return (is_array($data) || $data instanceof Traversable) &&
        isset($data[0]);
    }
}
