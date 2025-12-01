<?php

namespace common\components;

use yii\base\Component;

class ErrorStandarts extends Component
{

    private const ERRORS = [
        // 1XXX - data required codes
        'EMAIL_REQUIRED' => [
            'code' => 1001,
            'message' => 'Email cannot be empty',
        ],
        'PASSWORD_REQUIRED' => [
            'code' => 1002,
            'message' => 'Password cannot be empty',
        ],
        'USERNAME_REQUIRED' => [
            'code' => 1003,
            'message' => 'Username cannot be empty',
        ],
        'START_DATE_REQUIRED' => [
            'code' => 1004,
            'message' => 'Start date cannot be empty',
        ],
        'END_DATE_REQUIRED' => [
            'code' => 1005,
            'message' => 'End date cannot be empty',
        ],
        // 'TYPE_REQUIRED' => [
        //     'code' => 1006,
        //     'message' => 'Type cannot be empty',
        // ],
        'USER_ID_REQUIRED' => [
            'code' => 1007,
            'message' => 'User ID cannot be empty',
        ],
        'NAME_REQUIRED' => [
            'code' => 1008,
            'message' => 'Name cannot be empty',
        ],
        // 'START_AT_REQUIRED' => [
        //     'code' => 1009,
        //     'message' => 'Start at cannot be empty',
        // ],
        // 'END_AT_REQUIRED' => [
        //     'code' => 1010,
        //     'message' => 'End at cannot be empty',
        // ],
        // 'DAYS_OF_WEEK_REQUIRED' => [
        //     'code' => 1011,
        //     'message' => 'Days of week cannot be empty',
        // ],
        // 'STATUS_REQUIRED' => [
        //     'code' => 1012,
        //     'message' => 'Status cannot be empty',
        // ],
        // 'PRIORITY_REQUIRED' => [
        //     'code' => 1013,
        //     'message' => 'Priority cannot be empty',
        // ],
        // 'OTHER_USER_ID_REQUIRED' => [
        //     'code' => 1014,
        //     'message' => 'Other user id cannot be empty',
        // ],
        // 'GROUP_ID_REQUIRED' => [
        //     'code' => 1015,
        //     'message' => 'Group ID cannot be empty',
        // ],
        // 'VISIBILITY_REQUIRED' => [
        //     'code' => 1016,
        //     'message' => 'Visibility cannot be empty',
        // ],

        // 2XXX - invalid data
        'INVALID_PASSWORD' => [
            'code' => 2001,
            'message' => 'Invalid password',
        ],
        'INVALID_EMAIL_FORMAT' => [
            'code' => 2002,
            'message' => 'Invalid email format',
        ],
        'INVALID_START_DATE' => [
            'code' => 2003,
            'message' => 'Invalid start date format',
        ],
        'INVALID_END_DATE' => [
            'code' => 2004,
            'message' => 'Invalid end date format',
        ],
        // 'INVALID_DAYS_OF_WEEK' => [
        //     'code' => 2005,
        //     'message' => 'Days of week is not in range',
        // ],
        // 'INVALID_START_AT' => [
        //     'code' => 2006,
        //     'message' => 'Invalid start at format, should be hh:mm:ss',
        // ],
        // 'INVALID_END_AT' => [
        //     'code' => 2007,
        //     'message' => 'Invalid end at format, should be hh:mm:ss',
        // ],
        // 'INVALID_STATUS' => [
        //     'code' => 2008,
        //     'message' => 'Status is not in range',
        // ],
        'INVALID_NAME' => [
            'code' => 2009,
            'message' => 'Invalid name format',
        ],
        'INVALID_NAME_TOO_LONG' => [
            'code' => 2010,
            'message' => 'Name max length is 255 characters',
        ],
        'INVALID_ROLE_TYPE' => [
            'code' => 2011,
            'message' => 'Role shoul be string',
        ],
        'INVALID_ROLE' => [
            'code' => 2012,
            'message' => 'Role is not in range',
        ],
        // 'INVALID_START_AT_GREATER_END_AT' => [
        //     'code' => 2011,
        //     'message' => 'Start at cannot be greater than End at',
        // ],
        // 'INVALID_DESCRIPTION' => [
        //     'code' => 2012,
        //     'message' => 'Invalid description format',
        // ],
        // 'INVALID_PRIORITY' => [
        //     'code' => 2013,
        //     'message' => 'Priority is not in range',
        // ],
        // 'INVALID_STATUS_TYPE' => [
        //     'code' => 2014,
        //     'message' => 'Status should be integer',
        // ],
        // 'INVALID_PRIORITY_TYPE' => [
        //     'code' => 2015,
        //     'message' => 'Priority should be integer',
        // ],
        // 'INVALID_RELATIONSHIP_SAME_USERS' => [
        //     'code' => 2016,
        //     'message' => 'Current user and other user cannot be the same',
        // ],
        // 'INVALID_OTHER_USER_ID_TYPE' => [
        //     'code' => 2017,
        //     'message' => 'Other user ID should be integer',
        // ],
        // 'INVALID_RELATIONSHIP_SAME_USER' => [
        //     'code' => 2018,
        //     'message' => 'This request cannot be accepted',
        // ],
        // 'INVALID_ROLE' => [
        //     'code' => 2019,
        //     'message' => 'Role is not in range',
        // ],
        // 'INVALID_VISIBILITY_TYPE' => [
        //     'code' => 2020,
        //     'message' => 'Visibility should be integer',
        // ],
        // 'INVALID_VISIBILITY' => [
        //     'code' => 2021,
        //     'message' => 'Visibility is not in range',
        // ],

        // 3XXX - data is already taken
        'EMAIL_TAKEN' => [
            'code' => 3001,
            'message' => 'Email has already been taken',
        ],
        'USERNAME_TAKEN' => [
            'code' => 3002,
            'message' => 'Username has already been taken',
        ],
        'NAME_TAKEN' => [
            'code' => 3003,
            'message' => 'Name has already been taken',
        ],

        // 4XXX - data already exist
        'PROGRAM_EXIST' => [
            'code' => 4001,
            'message' => 'Program already exist',
        ],
        'PROGRAM_USER_ROLE_EXIST' => [
            'code' => 4002,
            'message' => 'Program user role already exist',
        ],

        // 5XXX - data does not exist
        'USER_DOESNT_EXIST' => [
            'code' => 5001,
            'message' => 'User does not exist',
        ],
        'PROGRAM_DOESNT_EXIST' => [
            'code' => 5002,
            'message' => 'Program does not exist',
        ],

        // 7XXX - state based errors
        'CANT_CHANGE_WHEN_ANNOUNCED' => [
            'code' => 7001,
            'message' => 'Cant update program when state is ANNOUNCED',
        ],
        'CANT_CHANGE_WHEN_SUBMISSION' => [
            'code' => 7002,
            'message' => 'Cant update program when state is SUBMISSION',
        ],
        'CAN_DELETE_ONLY_WHEN_CREATED' => [
            'code' => 7003,
            'message' => 'Can delete program only when state is CREATED',
        ],
        'INVALID_STATE_TRANSITION' => [
            'code' => 7004,
            'message' => 'Program cannot transition to that state',
        ],

        // 8XXX - role based errors
        'ADMIN_CANT_MANAGE_PROGRAM' => [
            'code' => 8001,
            'message' => 'Admin cannot manage program',
        ],
        'PROGRAMER_ROLE_REQUIRED' => [
            'code' => 8002,
            'message' => 'Programmer role required to make this action',
        ],


        // 9XXX - errors connected to DB
        'ERROR_SAVING_PROGRAM' => [
            'code' => 9001,
            'message' => 'Error saving program',
        ],
        'ERROR_DELETING_PROGRAM' => [
            'code' => 9002,
            'message' => 'Error deleting program',
        ],
        'ERROR_SAVING_PROGRAM_USER_ROLE' => [
            'code' => 9003,
            'message' => 'Error saving program user role',
        ],

        'UNEXPECTED_ERROR' => [
            'code' => 999,
            'message' => 'Unexpected error',
        ],
    ];

    public static function all(): array
    {
        return self::ERRORS;
    }

    public static function get(string $key): array
    {
        return self::ERRORS[$key] ?? self::ERRORS['UNEXPECTED_ERROR'];
    }

    public static function getMessage(string $key): string
    {
        return self::ERRORS[$key]['message'] ?? self::ERRORS['UNEXPECTED_ERROR']['message'];
    }

    public static function getCode(string $key): int
    {
        return self::ERRORS[$key]['code'] ?? self::ERRORS['UNEXPECTED_ERROR']['code'];
    }

}