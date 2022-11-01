<?php
namespace Drupal\ucr_core;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Config\FileStorage;

class UCRCustoms {
    public function _init() { }

    public static function updateConfigFromStorage($service_name, $files_to_read = array(), $service="module", $read_dirs = array('install', 'optional')) {
        $messenger = \Drupal::messenger();
        $config = \Drupal::configFactory();
        $service_path = "";
        switch($service) {
            case "theme": {
                $handler = \Drupal::service('theme_handler');
                $service_path = $handler->getTheme($service_name)->getPath();
                break;
            }
            case "profile": {
                $service_path = drupal_get_path('profile', $service_name);
                break;
            }
            case "core": {
                $service_path = drupal_get_path('core');
                break;
            }
            default: {
                $handler = \Drupal::service('module_handler');
                $service_path = $handler->getModule($service_name)->getPath();
                break;
            }
        }

        $all_updated = TRUE;
        $paths = array();

        if(!is_array($files_to_read) || count($files_to_read) == 0) {
            \Drupal::logger($service_name)->critical('There are no files listed to be read for updating in the '.$service.': \''.$service_name.'\'.');
            $messenger->addMessage('There are no files listed to be read for updating in the '.$service.': \''.$service_name.'\'.', $messenger::TYPE_ERROR);
            return false;
        }

        foreach($read_dirs as $directories) {
            $paths[] = new FileStorage($service_path . '/config/'.$directories);
        }

        foreach ($files_to_read as $file_name) {
            $do_update = FALSE;
            $current_id = NULL;
            $config_file_data = NULL;

            foreach($paths as $config_path) {
                $config_file_data = $config_path->read($file_name);
                if($config_file_data) { break; }
            }

            if(!$config_file_data) {
                \Drupal::logger($service_name)->warning('Unable to read the file: '.$file_name);
                $messenger->addMessage('Unable to read the file: '.$file_name, $messenger::TYPE_WARNING);
                continue;
            }

            $current_config_data = $config->get($file_name);
            if($current_config_data) {
                $current_id = $current_config_data->get('uuid');
                if(!empty($current_id)) { $do_update = true; }
            }

            $entity_type = \Drupal::service('config.manager')->getEntityTypeIdByName($file_name);
            if($entity_type) { // Configuration is of an entity type.
                $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
                if ($do_update) {
                    $update_config_record = array_merge(array('uuid' => $current_id), $config_file_data);
                    $entity = $storage->createFromStorageRecord($update_config_record);
                    $entity->enforceIsNew(FALSE);
                } else {
                    $entity = $storage->createFromStorageRecord($config_file_data);
                }

                try {
                    $entity->save();
                } catch (\Exception $e) {
                    \Drupal::logger($service_name)->error('Saving of configuration file: \'' . $file_name . '\' has failed. Reason: ' . $e->getMessage());
                    $messenger->addMessage('Saving of configuration file: \'' . $file_name . '\' has failed . Reason: ' . $e->getMessage(), $messenger::TYPE_ERROR);
                    $all_updated = FALSE;
                }
            } else { // Configuration is entity-less, try and running it the old method.
                $complete_config_record = "";
                if($do_update) {
                    $complete_config_record = array_merge(array('uuid' => $current_id), $config_file_data);
                } else {
                    $complete_config_record = $config_file_data;
                }

                $current_config = $config->getEditable($file_name);
                $current_config->setData($complete_config_record)->save(TRUE);
            }
        }

        return $all_updated;
    }
}