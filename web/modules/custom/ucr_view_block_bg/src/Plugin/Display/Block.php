<?php

namespace Drupal\ucr_view_block_bg\Plugin\Display;

use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\Block\ViewsBlock;
use Drupal\ctools_views\Plugin\Display\Block as CtoolsBlock;
use Drupal\views\Plugin\views\filter\InOperator;

/**
 * Provides a Block display plugin that allows for greater control over Views
 * block settings.
 */
class Block extends CtoolsBlock {

    /**
     * {@inheritdoc}
     */
    public function optionsSummary(&$categories, &$options) {
        parent::optionsSummary($categories, $options);
        $filtered_allow = array_filter($this->getOption('allow'));
        $filter_options = [
            'items_per_page' => $this->t('Items per page'),
            'offset' => $this->t('Pager offset'),
            'pager' => $this->t('Pager type'),
            'hide_fields' => $this->t('Hide fields'),
            'sort_fields' => $this->t('Reorder fields'),
            'disable_filters' => $this->t('Disable filters'),
            'configure_sorts' => $this->t('Configure sorts'),
            'background' => $this->t('Background configure'),
        ];
        $filter_intersect = array_intersect_key($filter_options, $filtered_allow);

        $options['allow'] = array(
            'category' => 'block',
            'title' => $this->t('Allow settings'),
            'value' => empty($filtered_allow) ? $this->t('None') : implode(', ', $filter_intersect),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state) {
        parent::buildOptionsForm($form, $form_state);
        $options = $form['allow']['#options'];
        $options['background'] = $this->t('Background Configure');
        $form['allow']['#options'] = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm(ViewsBlock $block, array &$form, FormStateInterface $form_state) {
        $form = parent::blockForm($block, $form, $form_state);

        $allow_settings = array_filter($this->getOption('allow'));
        $block_configuration = $block->getConfiguration();

        if(!empty($allow_settings['background'])){
            $form['override']['background'] = array(
                '#type' => 'fieldset',
                '#title' => 'Block Background Configuration'
            );

            $form['override']['background']['background_image'] = array(
                '#type' => 'managed_file',
                '#title' => $this->t('Image'),
                '#description' => $this->t('Upload an image to be your background image.'),
                '#default_value' => isset($block_configuration['background']['image']) ? $block_configuration['background']['image'] : '',
                '#upload_location' => 'public://backgrounds'
            );

            $form['override']['background']['background_image_repeat'] = array(
                '#type' => 'select',
                '#title' => $this->t('Image Repeat'),
                '#options' => array(
                    'no-repeat' => $this->t('No Repeat'),
                    'repeat-x' => $this->t('Repeat Horizontally'),
                    'repeat-y' => $this->t('Repeat Vertically'),
                    'repeat' => $this->t('Repeat Both')
                )
            );

            $form['override']['background']['background_color'] = array(
                '#type' => 'color',
                '#title' => $this->t('Color'),
                '#default_value' => isset($block_configuration['background']['color']) ? $block_configuration['background']['color'] : '#ffffff'
            );

            $form['override']['background']['background_color_none'] = array(
                '#type' => 'checkbox',
                '#return_value' => 'none',
                '#title' => 'No Color',
                '#description' => $this->t('Select the color you would like, or check the box for no color.'),
                '#default_value' => isset($block_configuration['background']['color_none']) ? $block_configuration['background']['color_none'] : ''
            );

            $form['override']['background']['background_opacity'] = array(
                '#type' => 'number',
                '#title' => $this->t('Color Opacity'),
                '#description' => $this->t('Enter in the opacity level. 0 = transparent; 100 = solid.'),
                '#default_value' => isset($block_configuration['background']['opacity']) ? $block_configuration['background']['opacity'] : '100'
            );
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit(ViewsBlock $block, $form, FormStateInterface $form_state) {
        // Set default value for items_per_page if left blank.
        if (empty($form_state->getValue(array('override', 'items_per_page')))) {
            $form_state->setValue(array('override', 'items_per_page'), "none");
        }

        parent::blockSubmit($block, $form, $form_state);
        $configuration = $block->getConfiguration();
        $allow_settings = array_filter($this->getOption('allow'));

        if (!empty($allow_settings['background'])) {
            $background = $form_state->getValue(['override', 'background', 'background_image']);

            // fix bug in Drupal which does not put the image as permanent
            if(array_key_exists(0, $background)){
                $fid = $form_state->getValue(['override', 'background', 'background_image'])[0];
                /** @var \Drupal\file\Entity\File $file */
                $file = \Drupal\file\Entity\File::load($fid);
                /** @var \Drupal\file\FileUsage\DatabaseFileUsageBackend $file_usage */
                $file_usage = \Drupal::service('file.usage');
                $file_usage->add($file, 'ctools_views', 'image', 1);

                $uri = $file->getFileUri();
                $configuration['background']['image'] = $background;
                $configuration['background']['image_path'] = file_create_url($uri);
            } else {
                $configuration['background']['image'] = '';
                $configuration['background']['image_path'] = '';
            }

            $configuration['background']['image_repeat'] = $form_state->getValue(['override', 'background', 'background_image_repeat']);
            $configuration['background']['color'] = $form_state->getValue(['override', 'background', 'background_color']);
            $configuration['background']['color_none'] = $form_state->getValue(['override', 'background', 'background_color_none']);
            $configuration['background']['opacity'] = $form_state->getValue(['override', 'background', 'background_opacity']);
        }

        $block->setConfiguration($configuration);
    }
}
