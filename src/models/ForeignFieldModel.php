<?php

namespace towardstudio\linkfield\models;

use Craft;
use craft\base\ElementInterface;
use craft\base\Model;
use craft\elements\MatrixBlock;
use Exception;
use towardstudio\linkfield\helpers\ArrayHelper;
use towardstudio\linkfield\helpers\ElementHelpers;
use towardstudio\linkfield\fields\ForeignField;
use yii\base\InvalidConfigException;

/**
 * Class ForeignModel
 */
abstract class ForeignFieldModel extends Model
{
  /**
   * @var ForeignField
   */
  protected ForeignField $_field;

  /**
   * @var ElementInterface|null
   */
  protected ?ElementInterface $_owner;

  /**
   * @var ElementInterface|null|false
   */
  protected null|false|ElementInterface $_root = false;


  /**
   * ForeignModel constructor.
   *
   * @param ForeignField $field
   * @param ElementInterface|null $owner
   * @param array $config
   */
  public function __construct(ForeignField $field, ElementInterface $owner = null, array $config = []) {
    $this->_field = $field;
    $this->_owner = $owner;

    parent::__construct($config);
  }

  /**
   * @return array
   */
  public function __debugInfo(): array {
    return $this->attributes;
  }

  /**
   * @param string $attribute
   * @return string
   */
  public function getAttributeLabel($attribute): string {
    return $this->translate(parent::getAttributeLabel($attribute));
  }

  /**
   * @return ForeignField
   */
  public function getField(): ForeignField {
    return $this->_field;
  }

  /**
   * @return ElementInterface|null
   * @noinspection PhpUnused (API)
   */
  public function getOwner(): ElementInterface|null {
    return $this->_owner;
  }

  /**
   * @return ElementInterface|null
   * @throws InvalidConfigException
   */
  public function getRoot(): ElementInterface|null {
    if ($this->_root === false) {
      $this->_root = is_null($this->_owner)
        ? null
        : self::toParentElement($this->_owner);
    }

    return $this->_root;
  }

  /**
   * @return bool
   */
  public function isEmpty(): bool {
    return false;
  }

  /**
   * @param ElementInterface|null $owner
   * @return $this
   * @noinspection PhpUnused (API)
   */
  public function withOwner(ElementInterface $owner = null): static {
    if ($this->_owner === $owner) {
      return $this;
    }

    $model = clone $this;
    $model->_owner = $owner;
    $model->_root = false;
    return $model;
  }

  /**
   * @return array
   */
  public function __serialize() : array {
    return [
      '_attributes' => $this->attributes,
      '_field' => $this->_field->handle,
      '_owner' => ElementHelpers::serialize($this->_owner),
    ];
  }

  /**
   * @param array $data
   * @throws Exception
   */
  public function __unserialize(array $data): void {
    $this->_owner = ElementHelpers::unserialize(
      ArrayHelper::get($data, '_owner')
    );

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    $this->_field = Craft::$app->getFields()->getFieldByHandle(
      (string)ArrayHelper::get($data, '_field', ''),
      $this->_owner->getFieldContext() ?? null
    );

    $attributes = ArrayHelper::get($data, '_attributes');
    if (is_array($attributes)) {
      $this->setAttributes($attributes, false);
    }
  }


  // Protected methods
  // -----------------

  /**
   * @param string $message
   * @return string
   */
  protected function translate(string $message): string {
    return $this->_field::t($message);
  }


  // Private methods
  // ---------------

  /**
   * @param ElementInterface $element
   * @return ElementInterface
   * @throws InvalidConfigException
   */
  private static function toParentElement(ElementInterface $element): ElementInterface {
    if ($element instanceof MatrixBlock) {
      return self::toParentElement(self::toMatrixParentElement($element));
    }

    if (is_a($element, 'verbb\supertable\elements\SuperTableBlockElement')) {
      /** @noinspection PhpPossiblePolymorphicInvocationInspection */
      return self::toParentElement($element->getOwner());
    }

    return $element;
  }

  /**
   * @param MatrixBlock $element
   * @return ElementInterface
   * @throws Exception
   */
  private static function toMatrixParentElement(MatrixBlock $element): ElementInterface {
    try {
      $owner = $element->getOwner();
      if ($owner->id == $element->primaryOwnerId) {
        return $owner;
      }
    } catch (\Throwable) {
      // If the owner is trashed, we'll get an exception here. Ignore it and try fetching the element.
    }

    $sites = [$element->siteId, '*'];
    $elements = Craft::$app->getElements();

    while (count($sites)) {
      $owner = $elements->getElementById($element->primaryOwnerId, null, array_shift($sites), ['trashed' => null]);
      if ($owner) {
        return $owner;
      }
    }

    throw new Exception("Invalid parent id $element->primaryOwnerId for matrix block with id $element->id in site $element->siteId.");
  }
}
