<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\FocusSchedule;


class FocusScheduleForm extends FocusSchedule
{
    public function getModeName()
    {
        return match ($this->mode) {
            self::MODE_REGULAR => 'Regular',
            self::MODE_POMODORO => 'Pomodoro',
            default => 'Unknown',
        };
    }
    
    public function getModeNameBadge()
    {
        return match ($this->mode) {
            self::MODE_REGULAR => '<span class="badge badge-primary">REGULAR</span>',
            self::MODE_POMODORO => '<span class="badge badge-danger">POMODORO 🍅</span>',
            default => '<span class="badge badge-light">Unknown</span>',
        };
    }

    public static function getModeFilters()
    {
        return [
            self::MODE_REGULAR => 'Regular',
            self::MODE_POMODORO => 'Pomodoro',
        ];
    }


    public function getStatusName()
    {
        return match ($this->status) {
            self::STATUS_INACTIVE => 'INACTIVE',
            self::STATUS_ACTIVE => 'ACTIVE',
            default => 'Unknown',
        };
    }
    public function getStatusNameBadge()
    {
        return match ($this->status) {
            self::STATUS_INACTIVE => '<span class="badge badge-danger">INACTIVE</span>',
            self::STATUS_ACTIVE => '<span class="badge badge-success">ACTIVE</span>',
            default => '<span class="badge badge-light">Unknown</span>',
        };
    }

    public static function getStatusFilters()
    {
        return [
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
        ];
    }


}
