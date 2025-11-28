<?php

namespace backend\models;

use common\models\Link;


class LinkForm extends Link
{

    public function behaviors() {
        return parent::behaviors();
    }

    public function getIsHeaderName()
    {
        return match ($this->is_header) {
            0 => 'LINK',
            1 => 'HEADER',
            default => 'Unknown',
        };
    }
    public function getIsHeaderNameBadge()
    {
        return match ($this->is_header) {
            0 => '<span class="badge badge-danger">LINK</span>',
            1 => '<span class="badge badge-success">HEADER</span>',
            default => '<span class="badge badge-light">Unknown</span>',
        };
    }

    public static function getIsHeaderFilters()
    {
        return [
            0 => 'Link',
            1 => 'Header',
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
