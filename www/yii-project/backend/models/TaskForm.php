<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Task;


class TaskForm extends Task
{
    public function getPriorityName()
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            default => 'Unknown',
        };
    }
    
    public function getPriorityNameBadge()
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => '<span class="badge badge-success">LOW</span>',
            self::PRIORITY_MEDIUM => '<span class="badge badge-warning">MEDIUM</span>',
            self::PRIORITY_HIGH => '<span class="badge badge-danger">HIGH</span>',
            default => '<span class="badge badge-light">Unknown</span>',
        };
    }

    public static function getPriorityFilters()
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
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
