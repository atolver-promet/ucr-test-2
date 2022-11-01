<?php

namespace Drupal\ucr_layouts\Plugin\Layout;

/**
 * Configurable two column layout plugin class.
 *
 * @internal
 *   Plugin classes are internal.
 */
class OneColumn extends LayoutBuilder {

    /**
     * {@inheritdoc}
     */
    protected function getWidthOptions() {
        return [];
    }

    protected function getCenteredOptions() {
        return [
            'center' => 'Centered',
            'full' => 'Full Width',
        ];
    }

    protected function getPaddingOptions() {
        return [
            'grid-padding-x' => 'Horizontal Padding Only',
            'grid-padding-x grid-padding-y' => 'Standard Padding',
            'grid-padding-y' => 'Vertical Padding Only',
            'none' => 'No Padding',
        ];
    }

    protected function getMarginOptions() {
        return [
            'none' => 'No Margins',
            'grid-margin-x' => 'Horizontal Margins Only',
            'grid-margin-y' => 'Vertical Margins Only',
            'grid-margin-x grid-margin-y' => 'Standard Margins',
        ];
    }

    protected function getBackgroundColorOptions() {
        return ['#F1F3F5,#E9ECEF,#DEE2E6,#CED4DA,#ADB5BD,#868E96,#495057,#343A40,#212529,#1D3661,#637EA4,#329AF0,#3AA4DC,#91AFCC,#A5C8EA,#D6336B,#802244,#14AABE,#FA5151,#F18200'];
    }

    protected function getColumnClassOptions() {
        return 1;
    }
}