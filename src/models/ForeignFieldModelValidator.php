<?php

namespace towardstudio\linkfield\models;

use towardstudio\linkfield\fields\ForeignField;
use yii\validators\Validator;

/**
 * Class ForeignModelValidator
 */
class ForeignFieldModelValidator extends Validator
{
  /**
   * @var ForeignField
   */
  public ForeignField $field;

  /**
   * @var string
   */
  public string $msgInstanceOf = '{attribute} must be an instance of {modelClass}.';

  /**
   * @var string
   */
  public string $msgErrors = '{attribute} contains errors.';


  /**
   * @param mixed $value
   * @return array|null
   */
  protected function validateValue(mixed $value): ?array {
    $field = $this->field;
    $modelClass = $field::modelClass();

    if (!is_a($value, $modelClass)) {
      return [
        $field::t($this->msgInstanceOf),
        ['modelClass' => $modelClass]
      ];
    }

    if (!$value->validate()) {
      return [$field::t($this->msgErrors), []];
    }

    return null;
  }
}
