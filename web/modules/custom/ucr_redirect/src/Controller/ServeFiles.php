<?php

namespace Drupal\ucr_redirect\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Controller for downloading journal articles and previews.
 */
class ServeFiles extends ControllerBase {

  /**
   * Downloads a watermarked article pdf.
   *
   * @param \Drupal\node\entity\Node $node
   *   The journal article node.
   * @param bool $preview
   *   Whether we are viewing the 1 page preview or the full pdf.
   *
   * @return Stream|RedirectResponse
   *   A file stream or redirect response.
   */
  public function serve($filepath1, $filepath2 = NULL, $filepath3 = NULL, $filepath4 = NULL, $filepath5 = NULL) {

    $filename  = basename($_SERVER['REQUEST_URI']);
    // Build route after the /sites/default/files.
    $path = $filepath1;
    $path .= ($filepath2) ? '/' . $filepath2 : '';
    $path .= ($filepath3) ? '/' . $filepath3 : '';
    $path .= ($filepath4) ? '/' . $filepath4 : '';
    $path .= ($filepath5) ? '/' . $filepath5 : '';

    $path = dirname($path);

    return new TrustedRedirectResponse('/sites/default/files/' . $path . '/' . $filename);
  }

}
