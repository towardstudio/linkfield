<?php

namespace towardstudio\linkfield\listeners;

use craft\feedme\events\RegisterFeedMeFieldsEvent;
use craft\feedme\fields\TypedLink;
use towardstudio\linkfield\helpers\feedme\LinkField;

/**
 * Class FeedMeListener
 */
class FeedMeListener
{
  /**
   * @param RegisterFeedMeFieldsEvent $event
   */
  static function onRegisterFeedMeFields(RegisterFeedMeFieldsEvent $event): void {
    $event->fields = array_filter($event->fields, function($field) {
      return $field != TypedLink::class;
    });

    $event->fields[] = LinkField::class;
  }
}
