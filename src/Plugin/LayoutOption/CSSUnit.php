<?php

namespace Drupal\layout_options_extra\Plugin\LayoutOption;

use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_options\OptionBase;

/**
 * Layout Option plugin to add one or more classes via string input.
 *
 * Multiple classes can be added by separating them with spaces.
 *
 * @LayoutOption(
 *   id = "layout_options_css_unit",
 *   label = @Translation("Layout CSS unit"),
 *   description = @Translation("A layout configuration option that adds a CSS unit to layout and/or regions")
 * )
 */
class CSSUnit extends OptionBase {

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
    return $this->createUnitElement($region, $form, $formState, $default);
  }

  /**
   * {@inheritdoc}
   */
  public function processOptionBuild(array $regions, array $build, string $region, $value) {
    return $this->processCSSUnitOptionBuild($regions, $build, $region, $value);
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
  public function processCSSUnitOptionBuild(array $regions, array $build, string $region, $value) {
    // if ($region == 'layout') {
      // if (!isset($build['#attributes'])) {
      //   $build['#attributes'] = [];
      // }
      // if (is_array($value)) {
      //   if (empty($build['#attributes'][$attribute])) {
      //     $build['#attributes'][$attribute] = $value;
      //   } else {
      //     $build['#attributes'][$attribute] = array_merge($build['#attributes'][$attribute], $value);
      //   }
      // } else {
      //   $build['#attributes'][$attribute][] = $value;
      // }
    // } elseif (array_key_exists($region, $regions)) {
    //   // $build[$region];
    //   if (!isset($build[$region]['#attributes'])) {

    //     $build[$region]['#attributes'] = [];
    //   }
    //   if (is_array($value)) {
    //     if (empty($build[$region]['#attributes'][$attribute])) {
    //       $build[$region]['#attributes'][$attribute] = $value;
    //     } else {
    //       $build[$region]['#attributes'][$attribute] = array_merge($build[$region]['#attributes'][$attribute], $value);
    //     }
    //   } else {
    //     $build[$region]['#attributes'][$attribute][] = $value;
    //   }
    // }
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
  public function createUnitElement(string $region, array $form, FormStateInterface $formState, $default) {
    $def = $this->getDefinition();
    $formRenderArray = [
      '#title' => $this->t($def['title']),
      '#description' => $this->t($def['description']),
      '#type' => 'container',
      '#attributes' => array('style' => ['display:flex']),
    ];
    $formRenderArray['value'] = [
      '#title' => $this->t($def['title']),
      '#description' => $this->t($def['description']),
      '#type' => 'number',
      '#default_value' => !empty($default['value']) ? $default['value'] : '',
    ];
    $formRenderArray['unit'] = [
      '#title' => $this->t('Unit'),
      '#description' => $this->t('CSS unit'),
      '#type' => 'select',
      '#options' => ['px' => 'px', 'em' => 'em', 'rem' => 'rem'],
      '#default_value' => !empty($default['unit']) ? $default['unit'] : 'px',
      '#attributes' => array('style' => ['min-width:60px']),
    ];

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

}
