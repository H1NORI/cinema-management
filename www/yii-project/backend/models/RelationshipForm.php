<?php

namespace backend\models;

use common\models\Relationship;


class RelationshipForm extends Relationship
{
    public function getTypeName()
    {
        return match ($this->type) {
            self::TYPE_PENDING_FIRST_SECOND => 'Request sent (first → second)',
            self::TYPE_PENDING_SECOND_FIRST => 'Request sent (second → first)',
            self::TYPE_FRIENDS => 'Friends',
            self::TYPE_FIRST_BLOCKED => 'Blocked (first → second)',
            self::TYPE_SECOND_BLOCKED => 'Blocked (second → first)',
            self::TYPE_BOTH_BLOCKED => 'Blocked both',
            default => 'Unknown',
        };
    }

    public function getTypeNameBadge()
    {
        return match ($this->type) {
            self::TYPE_PENDING_FIRST_SECOND => '<span class="badge badge-warning">Pending (first → second)</span>',
            self::TYPE_PENDING_SECOND_FIRST => '<span class="badge badge-warning">Pending (second → first)</span>',
            self::TYPE_FRIENDS => '<span class="badge badge-success">Friends</span>',
            self::TYPE_FIRST_BLOCKED => '<span class="badge badge-danger">Blocked (first → second)</span>',
            self::TYPE_SECOND_BLOCKED => '<span class="badge badge-danger">Blocked (second → first)</span>',
            self::TYPE_BOTH_BLOCKED => '<span class="badge badge-dark">Blocked by Both</span>',
            default => '<span class="badge badge-light">Unknown</span>',
        };
    }

    public static function getTypeFilters()
    {
        return [
            self::TYPE_PENDING_FIRST_SECOND => 'Pending (first → second)',
            self::TYPE_PENDING_SECOND_FIRST => 'Pending (second → first)',
            self::TYPE_FRIENDS => 'Friends',
            self::TYPE_FIRST_BLOCKED => 'Blocked (first → second)',
            self::TYPE_SECOND_BLOCKED => 'Blocked (second → first)',
            self::TYPE_BOTH_BLOCKED => 'Blocked both',
        ];
    }


}
