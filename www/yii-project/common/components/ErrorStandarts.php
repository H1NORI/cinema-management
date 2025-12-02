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
        'PROGRAM_ID_REQUIRED' => [
            'code' => 1009,
            'message' => 'Program ID cannot be empty',
        ],
        'START_TIME_REQUIRED' => [
            'code' => 1010,
            'message' => 'Start time cannot be empty',
        ],
        'END_TIME_REQUIRED' => [
            'code' => 1011,
            'message' => 'End time cannot be empty',
        ],
        'HANDLER_ID_REQUIRED' => [
            'code' => 1012,
            'message' => 'Handler ID cannot be empty',
        ],
        'SCORE_REQUIRED' => [
            'code' => 1013,
            'message' => 'Score cannot be empty',
        ],
        'COMMENTS_REQUIRED' => [
            'code' => 1014,
            'message' => 'Comments cannot be empty',
        ],
        'REJECTION_REASON_REQUIRED' => [
            'code' => 1015,
            'message' => 'Rejection reason cannot be empty',
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
        'INVALID_START_DATE' => [
            'code' => 2003,
            'message' => 'Invalid start date format',
        ],
        'INVALID_END_DATE' => [
            'code' => 2004,
            'message' => 'Invalid end date format',
        ],
        'INVALID_PROGRAM_ID_TYPE' => [
            'code' => 2005,
            'message' => 'Invalid program ID type',
        ],
        'INVALID_START_TIME' => [
            'code' => 2006,
            'message' => 'Invalid start time format',
        ],
        'INVALID_END_TIME' => [
            'code' => 2007,
            'message' => 'Invalid end time format',
        ],
        'INVALID_SCORE_TYPE' => [
            'code' => 2008,
            'message' => 'Invalid score type',
        ],
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
        'INVALID_COMMENTS_TYPE' => [
            'code' => 2013,
            'message' => 'Invalid comments type',
        ],
        'INVALID_REJECTION_REASON_TYPE' => [
            'code' => 2014,
            'message' => 'Invalid rejection reason type',
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
        'PROGRAM_SCREENING_REVIEW_EXIST' => [
            'code' => 4003,
            'message' => 'Screening review already exist',
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
        'PROGRAM_USER_ROLE_DOESNT_EXIST' => [
            'code' => 5003,
            'message' => 'Program user role does not exist',
        ],
        'SCREENING_DOESNT_EXIST' => [
            'code' => 5004,
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
        'CAN_CHABGE_ONLY_WHEN_CREATED' => [
            'code' => 7005,
            'message' => 'Can change screening only when state is CREATED',
        ],
        'PROGRAM_NOT_IN_SUBMISSION' => [
            'code' => 7006,
            'message' => 'Program is not in SUBMISION state',
        ],
        'PROGRAM_NOT_IN_ASSIGNMENT' => [
            'code' => 7007,
            'message' => 'Program is not in ASSIGNMENT state',
        ],
        'CAN_CHABGE_ONLY_WHEN_SUBMITTED' => [
            'code' => 7008,
            'message' => 'Can change screening only when state is SUBMITTED',
        ],
        'PROGRAM_NOT_IN_REVIEW' => [
            'code' => 7009,
            'message' => 'Program is not in REVIEW state',
        ],
        'PROGRAM_NOT_IN_SCHEDULING_OR_DECISION' => [
            'code' => 7010,
            'message' => 'Program is not in SCHEDULING or DECISION state',
        ],
        'CAN_CHABGE_ONLY_WHEN_REVIWED' => [
            'code' => 7011,
            'message' => 'Can change screening only when state is REVIEWED',
        ],
        'PROGRAM_NOT_IN_FINAL_PUBLICATION' => [
            'code' => 7012,
            'message' => 'Program is not in FINAL_PUBLICATION state',
        ],
        'CAN_CHABGE_ONLY_WHEN_APPROVED' => [
            'code' => 7013,
            'message' => 'Can change screening only when state is APPROVED',
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
        'ADMIN_CANT_MANAGE_SCREENING' => [
            'code' => 8003,
            'message' => 'Admin cannot manage screening',
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
        'ERROR_SAVING_SCREENING' => [
            'code' => 9004,
            'message' => 'Error saving screening',
        ],
        'ERROR_DELETING_SCREENING' => [
            'code' => 9005,
            'message' => 'Error deleting screening',
        ],
        'ERROR_SAVING_SCREENING_REVIEW' => [
            'code' => 9006,
            'message' => 'Error saving screening review',
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