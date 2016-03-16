<?php

namespace Tests\Helpers;

use App\Helpers\ErrorCode;

trait VerifyResponse
{

    /**
     * Verify error response
     *
     * @param int $httpCode
     * @param string|null $errorCode
     * @param array $fields
     * @param array $notFields
     */
    protected function verifyErrorResponse(
        $httpCode,
        $errorCode = null,
        array $fields = [],
        array $notFields = []
    ) {
        $this->seeStatusCode($httpCode)
            ->seeJsonStructure([
                'fields',
                'code',
                'exec_time',
            ])->isJson();

        $json = $this->decodeResponseJson();

        $this->dontSeeFields($json, $notFields);

        $expected = [];
        if ($fields) {
            $this->seeJsonStructure(['fields' => $fields]);
            unset($json['fields']);
        } else {
            $expected['fields'] = [];
        }

        if ($errorCode !== null) {
            $expected['code'] = $errorCode;
        } else {
            unset($json['code']);
        }

        unset($json['exec_time']);

        $this->assertEquals($expected, $json);
    }

    /**
     * Verify standard 422 "validation failed" response
     *
     * @param array $fields
     * @param array $notFields Fields that should not be in response
     */
    protected function verifyValidationResponse(
        array $fields,
        array $notFields = []
    ) {
        $this->verifyErrorResponse(422, ErrorCode::VALIDATION_FAILED, $fields,
            $notFields);
    }

    /**
     * Verify if there are no given fields in given json array
     *
     * @param array $json
     * @param array $fields
     */
    protected function dontSeeFields(array $json, array $fields)
    {
        if (!$fields) {
            return;
        }

        $jsonFields = array_keys($json['fields']);

        foreach ($fields as $field) {
            $this->assertFalse(in_array($field, $jsonFields),
                'There is no ' . $field . ' in fields');
        }
    }
}
