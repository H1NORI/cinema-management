<?php

namespace backend\models;

use common\models\GroupMember;

class GroupMemberForm extends GroupMember
{

    public function behaviors()
    {
        return parent::behaviors();
    }

    public function getRoleName()
    {
        return match ($this->role) {
            self::ROLE_OWNER => 'Owner',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_MEMBER => 'Member',
            self::ROLE_GUEST => 'Guest',
            default => 'Unknown',
        };
    }

    public function getRoleNameBadge()
    {
        return match ($this->role) {
            self::ROLE_OWNER => '<span class="badge badge-primary">OWNER</span>',
            self::ROLE_ADMIN => '<span class="badge badge-info">ADMIN</span>',
            self::ROLE_MEMBER => '<span class="badge badge-success">MEMBER</span>',
            self::ROLE_GUEST => '<span class="badge badge-secondary">GUEST</span>',
            default => '<span class="badge badge-light">Unknown</span>',
        };
    }


    public static function getRoleFilters()
    {
        return [
            self::ROLE_OWNER => 'Owner',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_MEMBER => 'Member',
            self::ROLE_GUEST => 'Guest',
        ];
    }



}
