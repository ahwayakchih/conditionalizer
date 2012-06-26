<?php
	Class extension_Conditionalizer extends Extension{
	
		public function about() {
			return array('name' => __('Conditionalizer'),
						 'version' => '1.2',
						 'release-date' => '2012-06-26',
						 'author' => array('name' => 'Marcin Konicki',
										   'website' => 'http://ahwayakchih.neoni.net',
										   'email' => 'ahwayakchih@neoni.net'),
						 'description' => __('Allows to execute data-sources and/or save entries conditionally.')
			);
		}

		public function uninstall() {
			return Symphony::Database()->query("DROP TABLE `tbl_fields_conditionalizer`");
		}
		
		public function update($previousVersion) {
			// Update 1.0 installations
			if (version_compare($previousVersion, '1.1', '<')) {
				$fields = Symphony::Database()->fetchCol('field_id', 'SELECT f.field_id FROM `tbl_fields_filter` AS f');
				if (!empty($fields)) {
					foreach ($fields as $id) {
						Symphony::Database()->query("ALTER TABLE `tbl_entries_data_{$id}` ADD `value` enum('yes','no') DEFAULT 'yes'");
					}
				}
				Symphony::Database()->query("ALTER TABLE `tbl_fields_filter` ADD `filter_publish_errors` enum('yes','no') DEFAULT 'no'");
			}

			// Convert from "Field: Filter" to "Conditionalizer"
			if (version_compare($previousVersion, '1.2', '<')) {
				$fields = Symphony::Database()->fetchCol('field_id', 'SELECT f.field_id FROM `tbl_fields_filter` AS f');
				if (!empty($fields)) {
					foreach ($fields as $id) {
						// TODO: convert data-sources that filter by this field, by moving filter expression into Conditionalizer expression.
					}
				}
				Symphony::Database()->query("ALTER TABLE `tbl_fields_filter` RENAME TO `tbl_fields_conditionalizer`");
				Symphony::Database()->query("ALTER TABLE `tbl_fields_conditionalizer` CHANGE `filter_publish` `expression` TEXT default ''");
				Symphony::Database()->query("ALTER TABLE `tbl_fields_conditionalizer` CHANGE `filter_publish_errors` `show_errors` enum('yes','no') NOT NULL default 'no'");
				Symphony::Database()->query("ALTER TABLE `tbl_fields_conditionalizer` DROP COLUMN `filter_datasource`");

				Symphony::Database()->query("UPDATE `tbl_fields` SET `type` = 'conditionalizer' WHERE `type` = 'filter'");
			}

			return true;
		}

		public function install() {
			return Symphony::Database()->query("CREATE TABLE `tbl_fields_conditionalizer` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`field_id` int(11) unsigned NOT NULL,
				`expression` TEXT default '',
				`show_errors` enum('yes','no') NOT NULL default 'no',
				PRIMARY KEY  (`id`),
				KEY `field_id` (`field_id`)
			)");
		}

	}

