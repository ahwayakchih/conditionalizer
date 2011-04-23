<?php
	Class extension_filterField extends Extension{
	
		public function about(){
			return array('name' => __('Field: Filter'),
						 'version' => '1.1',
						 'release-date' => '2011-04-23',
						 'author' => array('name' => 'Marcin Konicki',
										   'website' => 'http://ahwayakchih.neoni.net',
										   'email' => 'ahwayakchih@neoni.net'),
						 'description' => __('Allows to filter datasource and publishing with expressions and parameters.')
			);
		}

		public function uninstall(){
			return Symphony::Database()->query("DROP TABLE `tbl_fields_filter`");
		}
		
		public function update($previousVersion){
			return true;
		}

		public function install(){
			return Symphony::Database()->query("CREATE TABLE `tbl_fields_filter` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`field_id` int(11) unsigned NOT NULL,
				`filter_publish` TEXT default '',
				`filter_datasource` enum('yes','no') NOT NULL default 'no',
				PRIMARY KEY  (`id`),
				KEY `field_id` (`field_id`)
			)");
		}

	}

