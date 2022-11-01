<?php

namespace Drupal\ucr_layouts\Plugin\Layout;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Base class of layouts with configurable widths.
 *
 * @internal
 *   Plugin classes are internal.
 */
abstract class LayoutBuilder extends LayoutDefault implements PluginFormInterface {
    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration()
    {
        $default_config = array();

        $width_classes = array_keys($this->getWidthOptions());
        if(count($width_classes) > 0) { $default_config['column_widths'] = array_shift($width_classes); }

        $centered_options = array_keys($this->getCenteredOptions());
        if(count($centered_options) > 0) { $default_config['centered_options'] = array_shift($centered_options); }

        $padding_options = array_keys($this->getPaddingOptions());
        if(count($padding_options) > 0) { $default_config['padding_options'] = array_shift($padding_options); }

        $margin_options = array_keys($this->getMarginOptions());
        if(count($margin_options) > 0) { $default_config['margin_options'] = array_shift($margin_options); }

        $bg_color_options = $this->getBackgroundColorOptions();
        if(count($bg_color_options) > 0) { $default_config['bg_colors'] = array_shift($bg_color_options); }

        $column_class_options = $this->getColumnClassOptions();
        if(!empty($column_class_options) && is_numeric($column_class_options)) {
            $default_config['column_classes'] = array();
            for($i = 1; $i <= $column_class_options; $i++) {
                $default_config['column_classes']['column_'.$i] = '';
            }
        }

        return $default_config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
        $width_options = $this->getWidthOptions();
        if(count($width_options) > 0) {
            $form['column_widths'] = [
                '#type' => 'select',
                '#title' => $this->t('Column Widths'),
                '#default_value' => $this->configuration['column_widths'],
                '#options' => $width_options,
                '#description' => $this->t('Choose the column widths for this layout.'),
            ];
        }

        $centered_options = $this->getCenteredOptions();
        if(count($centered_options) > 0) {
            $form['centered_options'] = [
                '#type' => 'select',
                '#title' => $this->t('Full / Centered Layout'),
                '#default_value' => $this->configuration['centered_options'],
                '#options' => $centered_options,
                '#description' => $this->t('Choose if you want this layout to be centered or stretch full screen.'),
            ];
        }

        $padding_options = $this->getPaddingOptions();
        if(count($padding_options) > 0) {
            $form['padding_options'] = [
                '#type' => 'select',
                '#title' => $this->t('Padding Options'),
                '#default_value' => $this->configuration['padding_options'],
                '#options' => $padding_options,
                '#description' => $this->t('Choose the padding you want for the layout grid.'),
            ];
        }

        $margin_options = $this->getMarginOptions();
        if(count($margin_options) > 0) {
            $form['margin_options'] = [
                '#type' => 'select',
                '#title' => $this->t('Margin Options'),
                '#default_value' => $this->configuration['margin_options'],
                '#options' => $margin_options,
                '#description' => $this->t('Choose the margin you want for the layout grid.'),
            ];
        }

/* COMMENTED OUT THE BACKGROUND COLOR UNTIL THE COLOR_FIELD CAN BE IMPLEMENTED.
        $bg_color_options = $this->getBackgroundColorOptions();
        if(count($bg_color_options) > 0) {
            $form['bg_colors'] = [
                '#type' => 'color',
                '#title' => $this->t('Background Colors'),
                '#default_value' => $this->configuration['bg_colors'],
                '#description' => $this->t('Choose a background color for the whole layout.'),
            ];
        }
*/

        $column_class_options = $this->getColumnClassOptions();
        if(!empty($column_class_options) && is_numeric($column_class_options)) {
            $form['advanced_info'] = [
                '#type' => 'html_tag',
                '#tag' => 'h3',
                '#value' => $this->t('Advanced Settings'),
            ];

            $form['advanced_class_info'] = [
                '#markup' => '<div class="description">You can add CSS classes to further expand on the columns functionality. You can use the <a href="https://foundation.zurb.com/sites/docs/xy-grid.html" target="_blank">Zurb Foundation Classes</a>, or custom CSS classes. Separate each class with a space.</div>',
                '#allowed_tags' => [
                    'div',
                    'a',
                ],
            ];

            for($i = 1; $i <= $column_class_options; $i++) {
                $form['adv_column_class_'.$i] = [
                    '#type' => 'textfield',
                    '#title' => $this->t('Classes for Column '.$i),
                    '#default_value' => $this->configuration['column_classes']['column_'.$i],
                ];
            }
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    }

    /**
     * {@inheritdoc}
     */
    public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
        $col_widths = $form_state->getValue('column_widths');
        if($col_widths) { $this->configuration['column_widths'] = $col_widths; }

        $centered_set = $form_state->getValue('centered_options');
        if($centered_set) { $this->configuration['centered_options'] = $centered_set; }

        $padding_set = $form_state->getValue('padding_options');
        if($padding_set) { $this->configuration['padding_options'] = $padding_set; }

        $margin_set = $form_state->getValue('margin_options');
        if($margin_set) { $this->configuration['margin_options'] = $margin_set; }

        $bg_set = $form_state->getValue('bg_colors');
        if($bg_set) { $this->configuration['bg_colors'] = $bg_set; }

        $column_class_options = $this->getColumnClassOptions();
        if(!empty($column_class_options) && is_numeric($column_class_options)) {
            for($i = 1; $i <= $column_class_options; $i++) {
                $column_value = $form_state->getValue('adv_column_class_'.$i);
                $this->configuration['column_classes']['column_'.$i] = trim($column_value);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $regions)
    {
        $build = parent::build($regions);
        $build['#attributes']['ucr_layout_config'] = [];

        // Set the widths for the columns.
        if(array_key_exists('column_widths', $this->configuration)) {
            $widths = $this->configuration['column_widths'];
            if ($widths) {
                $width_sizes = explode('-', $widths);
                $total_columns = count($width_sizes);
                $col_number = 1;
                $columns = array();
                foreach ($width_sizes as $col_percent) {
                    $col_size = "";
                    if ($total_columns == 2) {
                        if ($col_percent == 25) {
                            $col_size = 3;
                        }
                        if ($col_percent == 33) {
                            $col_size = 4;
                        }
                        if ($col_percent == 50) {
                            $col_size = 6;
                        }
                        if ($col_percent == 66) {
                            $col_size = 8;
                        }
                        if ($col_percent == 75) {
                            $col_size = 9;
                        }
                    }
                    if ($total_columns == 3) {
                        if ($col_percent == 25) {
                            $col_size = 3;
                        }
                        if ($col_percent == 33) {
                            $col_size = 4;
                        }
                        if ($col_percent == 50) {
                            $col_size = 6;
                        }
                    }
                    if (!empty($col_size)) {
                        $columns['column_' . $col_number] = $col_size;
                        $col_number++;
                    }
                }

                if (count($columns) > 0) {
                    $build['#attributes']['ucr_layout_config']['columns'] = $columns;
                }
            }
        }

        // Set the css classes for the different columns.
        if(array_key_exists('column_classes', $this->configuration)) {
            $column_classes = $this->configuration['column_classes'];
            if (count($column_classes) > 0) {
                $col_number = 1;
                $columns = array();
                foreach ($column_classes as $c_class) {
                    $columns['column_' . $col_number] = (!empty($c_class) ? ' ' . $c_class : '');
                    $col_number++;
                }
                $build['#attributes']['ucr_layout_config']['classes'] = $columns;
            }
        }

        if(array_key_exists('centered_options', $this->configuration)) {
            $centered = $this->configuration['centered_options'];
            if ($centered) {
                $build['#attributes']['ucr_layout_config']['centered_options'] = $centered;
            }
        }

        if(array_key_exists('padding_options', $this->configuration)) {
            $padding = $this->configuration['padding_options'];
        }
        if(array_key_exists('margin_options', $this->configuration)) {
            $margins = $this->configuration['margin_options'];
        }
        $spacing = "";
        if($padding && ($padding !== 'none')) { $spacing = $padding; }
        if($margins && ($margins !== 'none')) { $spacing .= (!empty($spacing) ? ' ' : '').$margins; }

        $build['#attributes']['ucr_layout_config']['spacing_options'] = (!empty($spacing) ? ' ' : '').$spacing;

/*
         $bg_colors = $this->configuration['bg_colors'];
        if($bg_colors) {
            $build['#attributes']['ucr_layout_config']['bg_colors'] = $bg_colors;
        }
*/

        return $build;
    }

    /**
     * Gets the width options for the configuration form.
     *
     * The first option will be used as the default 'column_widths' configuration
     * value.
     *
     * @return string[]
     *   The width options array where the keys are strings that will be added to
     *   the CSS classes and the values are the human readable labels.
     */
    abstract protected function getWidthOptions();

    abstract protected function getCenteredOptions();

    abstract protected function getPaddingOptions();

    abstract protected function getMarginOptions();

    abstract protected function getBackgroundColorOptions();

    abstract protected function getColumnClassOptions();
}
