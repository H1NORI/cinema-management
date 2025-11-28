<?php

namespace backend\models;

use common\models\Group;

class GroupForm extends Group
{

    public function behaviors() {
        return parent::behaviors();
    }

    public function getVisibilityName()
    {
        return match ($this->visibility) {
            self::VISIBILITY_INVISIBLE => 'INVISIBLE',
            self::VISIBILITY_VISIBLE => 'VISIBLE',
            default => 'Unknown',
        };
    }
    public function getVisibilityNameBadge()
    {
        return match ($this->visibility) {
            self::VISIBILITY_INVISIBLE => '<span class="badge badge-danger">INVISIBLE</span>',
            self::VISIBILITY_VISIBLE => '<span class="badge badge-success">VISIBLE</span>',
            default => '<span class="badge badge-light">Unknown</span>',
        };
    }

    public static function getVisibilityFilters()
    {
        return [
            self::VISIBILITY_INVISIBLE => 'INVISIBLE',
            self::VISIBILITY_VISIBLE => 'VISIBLE',
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
