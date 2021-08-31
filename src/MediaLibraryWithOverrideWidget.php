<?php

namespace Drupal\entity_reference_override;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_reference_override\Plugin\Field\FieldWidget\EntityReferenceOverrideWidgetTrait;
use Drupal\media_library\Plugin\Field\FieldWidget\MediaLibraryWidget;

/**
 * Plugin implementation of the 'media_library_with_override_widget' widget.
 *
 * This plugin will only be made available if the media library module is
 * installed. The plugin annotation is on the placeholder class.
 *
 * @see \Drupal\entity_reference_override\Plugin\Field\FieldWidget\PlaceholderForMediaLibraryWithOverrideWidget
 */
class MediaLibraryWithOverrideWidget extends MediaLibraryWidget {

  use EntityReferenceOverrideWidgetTrait {
    formElement as singleFormElement;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    foreach ($items->referencedEntities() as $delta => $media_item) {
      $element['selection'][$delta] += $this->singleFormElement($items, $delta, [], $form, $form_state);
      $element['selection'][$delta]['edit']['#attributes'] = [
        'class' => [
          'media-library-item__edit',
        ],
      ];
    }
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  protected static function getFieldStateElementDepth(): int {
    return -3;
  }

}
