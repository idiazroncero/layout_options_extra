<?php

namespace Drupal\layout_options_extra\Plugin\LayoutOption;

use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_options\OptionBase;
use Drupal\helper\EntityBrowserFormTrait;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

/**
 * Layout Option plugin to add one or more classes via string input.
 *
 * Multiple classes can be added by separating them with spaces.
 *
 * @LayoutOption(
 *   id = "layout_options_media_reference",
 *   label = @Translation("Layout Media Reference"),
 *   description = @Translation("A layout configuration option that adds a Media Entity Reference")
 * )
 */
class MediaEntityReference extends OptionBase {

  use EntityBrowserFormTrait;

  // /**
  //  * {@inheritdoc}
  //  */
  // public function validateFormOption(array &$form, FormStateInterface $formState) {
  //   // $this->validateCssIdentifier($form, $formState, TRUE);
  // }

  /**
   * {@inheritdoc}
   */
  public function processFormOption(string $region, array $form, FormStateInterface $formState, $default) {
    return $this->createMediaEntityReference($region, $form, $formState, $default);
  }

  /**
   * {@inheritdoc}
   */
  public function processOptionBuild(array $regions, array $build, string $region, $value) {
    return $this->processMediaEntityBuild($regions, $build, $region, $value);
  }


  /**
   * Handle adding an option directly to the render array
   *
   * @param array $regions
   *   The regions to be built.
   * @param array $build
   *   The build render array.
   * @param string $region
   *   The region being processed.
   * @param mixed $value
   *   The value to used for the attribute.
   *
   * @return array
   *   The modified build array.
   */
  public function processMediaEntityBuild(array $regions, array $build, string $region, $value) {

    $target_id = !empty($value) ? $value : false;
    if (!empty($target_id)) {
      $media_id = explode(':', $target_id)[1];
      $media = Media::load($media_id);
      /** @var \Drupal\media\MediaSourceInterface $file_source */
      $file_source = $media->getSource();
      $file_id = $file_source->getSourceFieldValue($media);
      $file = File::load($file_id);
      $build['#settings']['layout_image_url'] = $file->url();
    }

    return $build;
  }


  /**
   * Utility function to add a number field form element from the option def.
   *
   * YAML definition should contain the following definition settings:
   *
   *     title: 'Option title'
   *     description: 'Option description'
   *     default: 'Option default value' or ''
   *
   * @param string $region
   *   The region to create this form.
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $formState
   *   The form state object.
   * @param mixed $default
   *   The default value to use.
   *
   * @return array
   *   The modified form
   */
  public function createMediaEntityReference(string $region, array $form, FormStateInterface $formState, $default) {
    $def = $this->getDefinition();
    $formRenderArray = $this->getEntityBrowserForm(
      'media_entity_browser_modal', // Entity Browser config entity ID
      $default, // Default value as a string
      1, // Cardinality
      'preview' // The view mode to use when displaying the entity in the selected entities table
    );

    if (isset($def['weight'])) {
      $formRenderArray['#weight'] = $def['weight'];
    }
    $optionId = $this->getOptionId();
    if ($region == 'layout') {
      $form[$optionId] = $formRenderArray;
    } else {
      $form[$region][$optionId] = $formRenderArray;
    }
    return $form;
  }


  /**
   * Converts form values to valid option defaults.
   *
   * @param mixed $values
   *   The form values to normalize.
   *
   * @return string|string[]
   *   The normalized / sanitized values.
   */
  public function getNormalizedValues($values) {
    return $values['browser']['entity_ids'];
  }

}
