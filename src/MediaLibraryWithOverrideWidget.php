<?php

namespace Drupal\entity_reference_override;

use Drupal\Component\Utility\NestedArray;
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
  public static function updateOverrideFieldState(array $form, FormStateInterface $form_state) {
    $button = $form_state->getTriggeringElement();

    $element = NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -2));

    $user_input = NestedArray::getValue($form_state->getUserInput(), $element['#parents']);
    $values = NestedArray::getValue($form_state->getValues(), $element['#parents']);

    foreach ($user_input as $key => $value) {
      $values[$key]['overwritten_property_map'] = $value['overwritten_property_map'] ?? '{}';
    }

    unset($values['add_more']);

    $element = NestedArray::getValue($form, array_slice($button['#array_parents'], 0, -3));

    $field_state = static::getWidgetState($element['#field_parents'], $element['#field_name'], $form_state);
    $field_state['items'] = $values;
    static::setWidgetState($element['#field_parents'], $element['#field_name'], $form_state, $field_state);
  }

}
