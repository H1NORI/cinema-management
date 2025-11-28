<?php

namespace common\components;

use yii\base\Component;

class ErrorStandarts extends Component
{

    //TODO maybe I should divide them in categories by model
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
        'MODE_REQUIRED' => [
            'code' => 1004,
            'message' => 'Mode cannot be empty',
        ],
        'SESSION_ID_REQUIRED' => [
            'code' => 1005,
            'message' => 'Session id cannot be empty',
        ],
        'TYPE_REQUIRED' => [
            'code' => 1006,
            'message' => 'Type cannot be empty',
        ],
        'USER_ID_REQUIRED' => [
            'code' => 1007,
            'message' => 'User ID cannot be empty',
        ],
        'NAME_REQUIRED' => [
            'code' => 1008,
            'message' => 'Name cannot be empty',
        ],
        'START_AT_REQUIRED' => [
            'code' => 1009,
            'message' => 'Start at cannot be empty',
        ],
        'END_AT_REQUIRED' => [
            'code' => 1010,
            'message' => 'End at cannot be empty',
        ],
        'DAYS_OF_WEEK_REQUIRED' => [
            'code' => 1011,
            'message' => 'Days of week cannot be empty',
        ],
        'STATUS_REQUIRED' => [
            'code' => 1012,
            'message' => 'Status cannot be empty',
        ],
        'PRIORITY_REQUIRED' => [
            'code' => 1013,
            'message' => 'Priority cannot be empty',
        ],
        'OTHER_USER_ID_REQUIRED' => [
            'code' => 1014,
            'message' => 'Other user id cannot be empty',
        ],
        'GROUP_ID_REQUIRED' => [
            'code' => 1015,
            'message' => 'Group ID cannot be empty',
        ],
        'VISIBILITY_REQUIRED' => [
            'code' => 1016,
            'message' => 'Visibility cannot be empty',
        ],

        // 2XXX - invalid data
        'INVALID_PASSWORD' => [
            'code' => 2001,
            'message' => 'Invalid password',
        ],
        'INVALID_EMAIL_FORMAT' => [
            'code' => 2002,
            'message' => 'Invalid email format',
        ],
        'INVALID_MODE' => [
            'code' => 2003,
            'message' => 'Mode is not in range',
        ],
        'INVALID_TYPE' => [
            'code' => 2004,
            'message' => 'Type is not in range',
        ],
        'INVALID_DAYS_OF_WEEK' => [
            'code' => 2005,
            'message' => 'Days of week is not in range',
        ],
        'INVALID_START_AT' => [
            'code' => 2006,
            'message' => 'Invalid start at format, should be hh:mm:ss',
        ],
        'INVALID_END_AT' => [
            'code' => 2007,
            'message' => 'Invalid end at format, should be hh:mm:ss',
        ],
        'INVALID_STATUS' => [
            'code' => 2008,
            'message' => 'Status is not in range',
        ],
        'INVALID_NAME' => [
            'code' => 2009,
            'message' => 'Invalid name format',
        ],
        'INVALID_NAME_TOO_LONG' => [
            'code' => 2010,
            'message' => 'Name max length is 255 characters',
        ],
        'INVALID_START_AT_GREATER_END_AT' => [
            'code' => 2011,
            'message' => 'Start at cannot be greater than End at',
        ],
        'INVALID_DESCRIPTION' => [
            'code' => 2012,
            'message' => 'Invalid description format',
        ],
        'INVALID_PRIORITY' => [
            'code' => 2013,
            'message' => 'Priority is not in range',
        ],
        'INVALID_STATUS_TYPE' => [
            'code' => 2014,
            'message' => 'Status should be integer',
        ],
        'INVALID_PRIORITY_TYPE' => [
            'code' => 2015,
            'message' => 'Priority should be integer',
        ],
        'INVALID_RELATIONSHIP_SAME_USERS' => [
            'code' => 2016,
            'message' => 'Current user and other user cannot be the same',
        ],
        'INVALID_OTHER_USER_ID_TYPE' => [
            'code' => 2017,
            'message' => 'Other user ID should be integer',
        ],
        'INVALID_RELATIONSHIP_SAME_USER' => [
            'code' => 2018,
            'message' => 'This request cannot be accepted',
        ],
        'INVALID_ROLE' => [
            'code' => 2019,
            'message' => 'Role is not in range',
        ],
        'INVALID_VISIBILITY_TYPE' => [
            'code' => 2020,
            'message' => 'Visibility should be integer',
        ],
        'INVALID_VISIBILITY' => [
            'code' => 2021,
            'message' => 'Visibility is not in range',
        ],

        // 3XXX - data is already taken
        'EMAIL_TAKEN' => [
            'code' => 3001,
            'message' => 'Email has already been taken',
        ],
        'USERNAME_TAKEN' => [
            'code' => 3002,
            'message' => 'Username has already been taken',
        ],

        // 4XXX - data already exist
        'SESSION_EXIST' => [
            'code' => 4001,
            'message' => 'Session already exist',
        ],
        'FOCUS_SESSION_PAUSED_EXIST' => [
            'code' => 4002,
            'message' => 'Session already paused',
        ],
        'FOCUS_SESSION_RUNNING_EXIST' => [
            'code' => 4003,
            'message' => 'Session already running',
        ],
        'RELATIONSHIP_TYPE_FRIENDS_EXIST' => [
            'code' => 4004,
            'message' => 'This users are already friends',
        ],
        'RELATIONSHIP_TYPE_BLOCKED_EXIST' => [
            'code' => 4005,
            'message' => 'Someone have this user in block list',
        ],
        'RELATIONSHIP_TYPE_PENDING_EXIST' => [
            'code' => 4006,
            'message' => 'This users already have pending requests',
        ],

        // 5XXX - data does not exist
        'SESSION_DOESNT_EXIST' => [
            'code' => 5001,
            'message' => 'Session does not exist',
        ],
        'FOCUS_SCHEDULE_DOESNT_EXIST' => [
            'code' => 5002,
            'message' => 'Focus schedule does not exist',
        ],
        'TASK_DOESNT_EXIST' => [
            'code' => 5003,
            'message' => 'Task does not exist',
        ],
        'USER_DOESNT_EXIST' => [
            'code' => 5004,
            'message' => 'User does not exist',
        ],
        'RELATIONSHIP_DOESNT_EXIST' => [
            'code' => 5005,
            'message' => 'Relationship does not exist',
        ],
        'GROUP_DOESNT_EXIST' => [
            'code' => 5006,
            'message' => 'Group does not exist',
        ],

        // 9XXX - errors connected to DB
        'ERROR_SAVING_FOCUS_SESSION' => [
            'code' => 9001,
            'message' => 'Error saving focus session',
        ],
        'ERROR_SAVING_FOCUS_SESSION_EVENT' => [
            'code' => 9002,
            'message' => 'Error saving focus session event',
        ],
        'ERROR_SAVING_FOCUS_SCHEDULE' => [
            'code' => 9003,
            'message' => 'Error saving focus schedule',
        ],
        'ERROR_DELETING_FOCUS_SCHEDULE' => [
            'code' => 9004,
            'message' => 'Error deleting focus schedule',
        ],
        'ERROR_SAVING_TASK' => [
            'code' => 9005,
            'message' => 'Error saving task',
        ],
        'ERROR_DELETING_TASK' => [
            'code' => 9006,
            'message' => 'Error deleting task',
        ],
        'ERROR_SAVING_RELATIONSHIP' => [
            'code' => 9007,
            'message' => 'Error saving relationship',
        ],
        'ERROR_DELETING_RELATIONSHIP' => [
            'code' => 9008,
            'message' => 'Error deleting relationship',
        ],
        'ERROR_SAVING_GROUP_MEMBER' => [
            'code' => 9009,
            'message' => 'Error saving group member',
        ],
        'ERROR_SAVING_GROUP' => [
            'code' => 9010,
            'message' => 'Error saving group',
        ],
        // 'ERROR_SAVING_TOKEN' => [
        //     'code' => 9003,
        //     'message' => 'Error saving token',
        // ],

        'UNEXPECTED_ERROR' => [
            'code' => 999,
            'message' => 'Unexpected error',
        ],
    ];

    // public function errorsList(): array {
    //     return [
    //         1001 => 'Email cannot be empty',
    //         1002 => 'Password cannot be empty',
    //         1003 => 'User not found',
    //         1004 => 'Invalid password',
    //         1005 => 'Token cannot be saved',
    //         1006 => 'Invalid email format',
    //         1007 => 'Email has already been taken',
    //         1008 => 'Username cannot be empty',
    //         1009 => 'Username has already been taken',
    //         2001 => 'Mode cannot be empty',
    //         2002 => 'Mode is not in range',
    //         9999 => 'Unexpected error',
    //     ];
    // }

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