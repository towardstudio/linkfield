<?php

namespace towardstudio\linkfield\migrations;

use Craft;
use craft\db\Migration;

/**
 * m240424_113911_namespace migration.
 */
class m240424_113911_namespace extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        echo "update_namespace updating .\n";

        $this->update(
            '{{%fields}}',
            [
                'type' => 'towardstudio\linkfield\fields\LinkField',
            ],
            'type = :lenz',
            [
                'lenz' => 'lenz\linkfield\fields\LinkField',
            ]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m240424_113911_namespace cannot be reverted.\n";
        return false;
    }
}
