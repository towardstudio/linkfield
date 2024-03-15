<?php

namespace towardstudio\linkfield;

use Craft;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterGqlTypesEvent;
use craft\services\Fields;
use craft\services\Gql;
use craft\utilities\ClearCaches;
use craft\web\Application as WebApplication;
use towardstudio\linkfield\fields\LinkField;
use towardstudio\linkfield\listeners\ElementListenerState;
use towardstudio\linkfield\models\LinkGqlType;
use Throwable;
use yii\base\Event;

/**
 * Class Plugin
 *
 * @property listeners\ElementListener $elementListener
 */
class Plugin extends \craft\base\Plugin
{
  /**
   * @inheritDoc
   */
  public string $schemaVersion = '2.1.0';

  /**
   * @event events\LinkTypeEvent
   */
  const EVENT_REGISTER_LINK_TYPES = 'registerLinkTypes';


  /**
   * @return void
   */
  public function init(): void {
    parent::init();

    $this->setComponents([
      'elementListener' => listeners\ElementListener::class,
      'feedMe' => listeners\FeedMeListener::class,
    ]);

    Event::on(
      Fields::class,
      Fields::EVENT_REGISTER_FIELD_TYPES,
      [$this, 'onRegisterFieldTypes']
    );

    Craft::$app->on(
      WebApplication::EVENT_INIT,
      [$this, 'onAppInit']
    );

    Event::on(
      ClearCaches::class,
      ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
      [listeners\CacheListener::class, 'onRegisterCacheOptions']
    );

    Event::on(
      Gql::class,
      Gql::EVENT_REGISTER_GQL_TYPES,
      [$this, 'onRegisterGqlTypes']
    );

    $feedMeFields = 'craft\feedme\services\Fields';
    if (class_exists($feedMeFields)) {
      Event::on(
        $feedMeFields,
        $feedMeFields::EVENT_REGISTER_FEED_ME_FIELDS,
        [listeners\FeedMeListener::class, 'onRegisterFeedMeFields']
      );
    }
  }

  /**
   * @return void
   */
  public function onAppInit(): void {
    try {
      if (
        Craft::$app->isInstalled &&
        ElementListenerState::getInstance()->isCacheEnabled()
      ) {
        $this->elementListener->processStatusChanges();
      }
    } catch (Throwable $error) {
      Craft::error($error->getMessage());
    }
  }

  /**
   * @param RegisterComponentTypesEvent $event
   */
  public function onRegisterFieldTypes(RegisterComponentTypesEvent $event) {
    $event->types[] = LinkField::class;
  }

  /**
   * @param RegisterGqlTypesEvent $event
   */
  public function onRegisterGqlTypes(RegisterGqlTypesEvent $event) {
    $event->types[] = LinkGqlType::class;
  }
}
