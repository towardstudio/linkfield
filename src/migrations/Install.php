<?php

namespace towardstudio\linkfield\migrations;

use craft\db\Migration;
use towardstudio\linkfield\records\LinkRecord;

/**
 * Class Install
 */
class Install extends Migration
{
  /**
   * @return bool
   */
  public function safeUp(): bool {
    LinkRecord::createTable($this);
    return true;
  }

  /**
   * @return bool
   */
  public function safeDown(): bool {
    LinkRecord::dropTable($this);
    return true;
  }
}
