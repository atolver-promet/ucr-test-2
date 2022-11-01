<?php
namespace Drupal\ucr_article;

use Drupal\Component\Serialization\Yaml;
use Drupal\taxonomy\TermInterface;


class ArticleViews {
    public function _init() { }

    public static function createViewPage(TermInterface $term) {
        $config = \Drupal::configFactory();
        $base_view = $config->getEditable('views.view.article_content');

        $tid = $term->get('tid')->value;
        $name = $term->get('name')->value;
        $path = $term->get('field_at_base_path')->value;
        $current_displays = $base_view->get('display');
        $total_existing_pages = count($current_displays);

        $page_id = preg_replace("/-/", "_", 'ucr_articles_term_type_'.$tid.'_page');
        $view_title = $name." Page";
        $page_path = $path;
        $page_title = "Latest ".$name;
        $type_id = $tid;
        $type_title = $name;

        $file_path = DRUPAL_ROOT . '/modules/custom/ucr_core/modules/ucr_article/view_templates/page_template.yml';
        $file_contents = file_get_contents($file_path);
        $page_template = Yaml::decode($file_contents);

        $page_template['ucr_articles_page_template']['id'] = $page_id;
        $page_template['ucr_articles_page_template']['display_title'] = $view_title;
        $page_template['ucr_articles_page_template']['position'] = $total_existing_pages + 1;
        $page_template['ucr_articles_page_template']['display_options']['path'] = $page_path;
        $page_template['ucr_articles_page_template']['display_options']['title'] = $page_title;
        $page_template['ucr_articles_page_template']['display_options']['filters']['field_article_types_target_id']['value'][1] = (int)$type_id;

        $base_view->set('display.'.$page_id, $page_template['ucr_articles_page_template'])->save(TRUE);

        return true;
    }

    public static function createViewFeed(TermInterface $term) {
        $config = \Drupal::configFactory();
        $base_view = $config->getEditable('views.view.article_content');

        $tid = $term->get('tid')->value;
        $name = $term->get('name')->value;
        $path = $term->get('field_at_base_path')->value;
        $current_displays = $base_view->get('display');
        $total_existing_pages = count($current_displays);

        $page_id = preg_replace("/-/", "_", 'ucr_articles_term_type_'.$tid.'_feed');
        $view_title = $name." Feed";
        $page_path = $path."/feed";
        $page_title = "Latest ".$name." RSS Feed";
        $type_id = $tid;
        $type_title = $name;

        $file_path = DRUPAL_ROOT . '/modules/custom/ucr_core/modules/ucr_article/view_templates/feed_template.yml';
        $file_contents = file_get_contents($file_path);
        $page_template = Yaml::decode($file_contents);

        $page_template['ucr_articles_feed_template']['id'] = $page_id;
        $page_template['ucr_articles_feed_template']['display_title'] = $view_title;
        $page_template['ucr_articles_feed_template']['position'] = $total_existing_pages + 1;
        $page_template['ucr_articles_feed_template']['display_options']['path'] = $page_path;
        $page_template['ucr_articles_feed_template']['display_options']['title'] = $page_title;
        $page_template['ucr_articles_feed_template']['display_options']['filters']['field_article_types_target_id']['value'][1] = (int)$type_id;

        $base_view->set('display.'.$page_id, $page_template['ucr_articles_feed_template'])->save(TRUE);

        return true;
    }

    public static function updateViewPage(TermInterface $term) {
        $config = \Drupal::configFactory();
        $base_view = $config->getEditable('views.view.article_content');

        $tid = $term->get('tid')->value;
        $name = $term->get('name')->value;
        $path = $term->get('field_at_base_path')->value;

        $page_id = preg_replace("/-/", "_", 'ucr_articles_term_type_'.$tid.'_page');
        $view_title = $name." Page";
        $page_path = $path;
        $page_title = "Latest ".$name;

        $base_view->set('display.'.$page_id.'.display_title', $view_title)
            ->set('display.'.$page_id.'.display_options.path', $page_path)
            ->set('display.'.$page_id.'.display_options.title', $page_title)
            ->save(TRUE);

        return true;
    }

    public static function updateViewFeed(TermInterface $term) {
        $config = \Drupal::configFactory();
        $base_view = $config->getEditable('views.view.article_content');

        $tid = $term->get('tid')->value;
        $name = $term->get('name')->value;
        $path = $term->get('field_at_base_path')->value;

        $page_id = preg_replace("/-/", "_", 'ucr_articles_term_type_'.$tid.'_feed');
        $view_title = $name." Feed";
        $page_path = $path."/feed";
        $page_title = "Latest ".$name." RSS Feed";

        $base_view->set('display.'.$page_id.'.display_title', $view_title)
            ->set('display.'.$page_id.'.display_options.path', $page_path)
            ->set('display.'.$page_id.'.display_options.title', $page_title)
            ->save(TRUE);

        return true;
    }

    public static function deleteViewPage(TermInterface $term) {
        $config = \Drupal::configFactory();
        $base_view = $config->getEditable('views.view.article_content');

        $tid = $term->get('tid')->value;

        $page_id = preg_replace("/-/", "_", 'ucr_articles_term_type_' . $tid . '_page');
        $displays = $base_view->get('display');

        unset($displays[$page_id]);
        $base_view->set('display', $displays)->save(TRUE);

        return true;
    }

    public static function deleteViewFeed(TermInterface $term) {
        $config = \Drupal::configFactory();
        $base_view = $config->getEditable('views.view.article_content');

        $tid = $term->get('tid')->value;

        $page_id = preg_replace("/-/", "_", 'ucr_articles_term_type_' . $tid . '_feed');
        $displays = $base_view->get('display');

        unset($displays[$page_id]);
        $base_view->set('display', $displays)->save(TRUE);

        return true;
    }

}