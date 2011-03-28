<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class InsertLanguageDefinition {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      if ( isset($data['key']) && isset($data['value']) ) {
        $data = array($data);
      }

      $error = false;
      $in_transaction = false;

      if ( count($data) > 1 ) {
        $OSCOM_PDO->beginTransaction();

        $in_transaction = true;
      }

      $Qdef = $OSCOM_PDO->prepare('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');

      foreach ( $data as $d ) {
        $Qdef->bindInt(':languages_id', $d['id']);
        $Qdef->bindValue(':content_group', $d['group']);
        $Qdef->bindValue(':definition_key', $d['key']);
        $Qdef->bindValue(':definition_value', $d['value']);
        $Qdef->execute();

        if ( $Qdef->isError() ) {
          if ( $in_transaction === true ) {
            $OSCOM_PDO->rollBack();
          }

          $error = true;

          break;
        }
      }

      if ( ($error === false) && ($in_transaction === true) ) {
        $OSCOM_PDO->commit();
      }

      return !$error;
    }
  }
?>
