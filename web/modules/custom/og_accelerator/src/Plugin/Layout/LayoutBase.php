<?php

namespace Drupal\og_accelerator\Plugin\Layout;

use Drupal\Component\Serialization\Yaml;
use Drupal\Component\Utility\Bytes;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

/**
 * Configurable base layout for Accelerator.
 */
abstract class LayoutBase extends LayoutDefault implements PluginFormInterface
{
  /**
   * The layout name.
   *
   * @var string.
   */
  protected $layoutName;

  /**
   * Gets section settings.
   *
   * @return array
   */
  protected function getSectionSettings()
  {
    $form = [];
    $settings = $this->getYamlSettings();
    if (!empty($settings['basic_settings'])) {
      $form = array_merge($form, $settings['basic_settings']);
    }
    if (!empty($this->layoutName) && !empty($settings[$this->layoutName . '_settings'])) {
      $form = array_merge($form, $settings[$this->layoutName . '_settings']);
    }
    return $form;
  }

  /**
   * Gets yaml settings.
   *
   * @return array
   */
  protected function getYamlSettings()
  {
    static $all_settings = [];
    $module = $this->moduleName;
    $path = \Drupal::service('module_handler')->getModule($module)->getPath();
    if (!isset($all_settings[$path . '/' . $module])) {
      $all_settings[$path . '/' . $module] = Yaml::decode(file_get_contents($path . '/' . $module . '.section_settings.yml'));
    }
    return $all_settings[$path . '/' . $module];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    $fields = $this->getSectionSettings();
    $default = [];
    foreach ($fields as $field_name => $field_data) {
      if (!empty($fields[$field_name]['__default'])) {
        $default[$field_name] = $fields[$field_name]['__default'];
      }
    }
    return $default;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    // if current route is jsonapi, background_image returns media id, we need to convert it into file url
    $route_name = \Drupal::routeMatch()->getRouteName();
    if ($route_name === 'jsonapi.node--page.individual' &&
        $media_id = $this->configuration['background']['background_image']) {
        $this->configuration['background']['background_image'] = Media::load($media_id)->field_media_image->entity->createFileUrl();
    }

    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $fields = $this->getSectionSettings();
    foreach ($fields as $field_name => $field_data) {
      $combined_field_name = 'layout_settings[' . $field_name . ']';
      $combined_parents = ['layout_settings', $field_name];
      if (!empty($field_data['__parent'])) {
        $form[$field_data['__parent']][$field_name] = [];
        $pointer = &$form[$field_data['__parent']][$field_name];
        $value_pointer = &$this->configuration[$field_data['__parent']][$field_name];
        $combined_field_name = 'layout_settings[' . $field_data['__parent'] . '][' . $field_name . ']';
        $combined_parents = ['layout_settings', $field_data['__parent'], $field_name];
      } else {
        $form[$field_name] = [];
        $pointer = &$form[$field_name];
        $value_pointer = &$this->configuration[$field_name];
      }
      foreach ($field_data as $field_data_key => $field_data_value) {
        if (strpos(trim($field_data_key), '__') !== 0) {
          $pointer['#' . $field_data_key] = $field_data_value;
        }
        if ($field_data_key === 'options' && is_callable($field_data_value)) {
          $field_data_value = call_user_func($field_data_value);
          $pointer['#options'] = $field_data_value;
        }
        if ($field_data_key === '__default') {
          $pointer['#default_value'] = $field_data_value;
        }
      }
      if (isset($value_pointer)) {
        if (is_array($value_pointer)) {
          $value_pointer = array_filter($value_pointer);
        }
        $pointer['#default_value'] = $value_pointer;
      }
      if ($pointer['#type'] === 'callback' && !empty($field_data['__callback_widget']) && is_callable($field_data['__callback_widget'])) {
        $params = [];
        $params['field_data'] = $field_data;
        $params['field_name'] = $field_name;
        $params['combined_field_name'] = $field_name;
        $params['parents'] = 'layout_settings';
        $params['combined_parents'] = $combined_parents;
        $params['default_value'] = isset($pointer['#default_value']) ? $pointer['#default_value'] : '';
        $pointer = call_user_func($field_data['__callback_widget'], $params, $form_state);
      }
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state)
  {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
  {
    $this->configuration = $form_state->getValues();
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $regions)
  {
    $build = parent::build($regions);
    $fields = $this->getSectionSettings();
    if (empty($build['#attributes']['class'])) {
      $build['#attributes']['class'] = [];
    }
    $section_access = true;
    foreach ($fields as $field_name => $field_data) {
      if (!empty($field_data['__parent'])) {
        $settings = &$build['#settings'][$field_data['__parent']][$field_name];
      } else {
        $settings = &$build['#settings'][$field_name];
      }
      if (!empty($field_data['__visibility']) && is_callable($field_data['__visibility'])) {
        $section_access = call_user_func($field_data['__visibility'], $settings);
      }
      if (!empty($field_data['__attributes']) && !empty($settings)) {
        unset($pointer);
        if (!empty($field_data['__region'])) {
          $pointer = &$build[$field_data['__region']];
        } else {
          $pointer = &$build;
        }
        $attribute_value = '';
        switch ($field_data['type']) {
          case 'checkbox':
            if (!empty($field_data['__checked'])) {
              $attribute_value = $field_data['__checked'];
            }
            break;
          case 'managed_file':
            if (!empty($settings[0])) {
              $file = File::load($settings[0]);
              if (!empty($file)) {
                $attribute_value = $file->createFileUrl();
              }
            }
            break;
          case 'callback':
            if (!empty($field_data['__callback_render']) && is_callable($field_data['__callback_render'])) {
              $attribute_value = call_user_func($field_data['__callback_render'], $settings);
            }
            break;
          case 'textfield':
          case 'select':
          default:
            $attribute_value = $settings;
            break;
        }
        if (!empty($attribute_value) && $attribute_value != '-none-') {
          switch ($field_data['__attributes']) {
            case 'class':
              $pointer['#attributes']['class'][] = $attribute_value;
              break;
            case 'class-region':
              if (!empty($field_data['__selected']) && !empty($field_data['__selected'][$attribute_value])) {
                foreach ($field_data['__selected'][$attribute_value] as $region => $class) {
                  $pointer[$region]['#attributes']['class'][] = $class;
                }
              }
              break;
            case 'style':
              if (!empty($field_data['__property']['name'])) {
                $name = $field_data['__property']['name'];
                $prefix = $suffix = '';
                if (!empty($field_data['__property']['prefix'])) {
                  $prefix = $field_data['__property']['prefix'];
                }
                if (!empty($field_data['__property']['suffix'])) {
                  $suffix = $field_data['__property']['suffix'];
                }
                $pointer['#attributes']['style'][] = $name . ': ' . $prefix . $attribute_value . $suffix . ';';
              }
              break;
            default:
              $pointer['#attributes'][$field_data['__attributes']][] = $attribute_value;
              break;
          }
        }
      }
    }
    $build['#access'] = $section_access;
    return $build;
  }

  /**
   * Special field type 'callback' gets the build array from the callback's return value.
   * This callback renders media library widget.
   *
   * @param array $params The field parameters from configuration and generated in build.
   * @param FormStateInterface $form_state The current form state.
   * @return array
   */
  public static function fieldEntityBrowserImageBrowser(array $params, FormStateInterface $form_state)
  {
    $field_value = !empty($params['default_value']) ? $params['default_value'] : '';
    $field = [
      '#type' => 'media_library',
      '#allowed_bundles' => ['image'],
      '#title' => !empty($field_data['title']) ? $field_data['title'] : $field_data['field_name'],
      '#default_value' => $field_value,
      '#description' => !empty($field_data['description']) ? $field_data['description'] : '',
    ];
    return $field;
  }

  /**
   * Special field type 'callback' requires its own rendering method.
   * This callback renders an image from media library field.
   *
   * @param int $media_id The media ID.
   * @return string
   */
  public static function renderEntityBrowserImageBrowser($media_id)
  {
    $media_url = '';
    if (!empty($media_id)) {
      $media = Media::load($media_id);
      $file = File::load($media->field_media_image->target_id);
      $media_url = $file->createFileUrl();
    }
    return $media_url;
  }
}
