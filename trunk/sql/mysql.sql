CREATE TABLE `vidshop_transactions` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `business` varchar(50) NOT NULL default '',
  `txn_id` varchar(20) NOT NULL default '',
  `item_name` varchar(60) NOT NULL default '',
  `item_number` varchar(40) NOT NULL default '',
  `quantity` varchar(6) NOT NULL default '',
  `invoice` varchar(40) NOT NULL default '',
  `custom` varchar(127) NOT NULL default '',
  `tax` varchar(10) NOT NULL default '',
  `option_name1` varchar(60) NOT NULL default '',
  `option_selection1` varchar(127) NOT NULL default '',
  `option_name2` varchar(60) NOT NULL default '',
  `option_selection2` varchar(127) NOT NULL default '',
  `memo` text NOT NULL,
  `payment_status` varchar(15) NOT NULL default '',
  `payment_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `txn_type` varchar(15) NOT NULL default '',
  `mc_gross` varchar(10) NOT NULL default '',
  `mc_fee` varchar(10) NOT NULL default '',
  `mc_currency` varchar(5) NOT NULL default '',
  `settle_amount` varchar(12) NOT NULL default '',
  `exchange_rate` varchar(10) NOT NULL default '',
  `first_name` varchar(127) NOT NULL default '',
  `last_name` varchar(127) NOT NULL default '',
  `address_street` varchar(127) NOT NULL default '',
  `address_city` varchar(127) NOT NULL default '',
  `address_state` varchar(127) NOT NULL default '',
  `address_zip` varchar(20) NOT NULL default '',
  `address_country` varchar(127) NOT NULL default '',
  `address_status` varchar(15) NOT NULL default '',
  `payer_email` varchar(127) NOT NULL default '',
  `payer_status` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `vidshop_translog` (
  `id` int(11) NOT NULL auto_increment,
  `log_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `payment_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `logentry` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `vidshop_video_category` (                    
	`cid` INT(13) UNSIGNED NOT NULL AUTO_INCREMENT,  
	`weight` INT(13) UNSIGNED DEFAULT '1',
	`name` VARCHAR(128) DEFAULT NULL,                
	`image` VARCHAR(255) DEFAULT NULL,
	`description` MEDIUMTEXT,                        
	PRIMARY KEY (`cid`)                              
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `vidshop_video` (                            
        `id` INT(13) UNSIGNED NOT NULL AUTO_INCREMENT,     
	`cid` INT(13) UNSIGNED DEFAULT '0',                
	`name` VARCHAR(128) DEFAULT NULL,                  
	`image` VARCHAR(255) DEFAULT NULL,                 
	`description` MEDIUMTEXT,                          
	`download` VARCHAR(255) DEFAULT NULL,              
	`price` DECIMAL(10,4) DEFAULT '0.0000',            
	`currency` ENUM('GBP','USD','AUD') DEFAULT 'USD',  
	`embedded` MEDIUMTEXT,                             
	`hits` INT(6) UNSIGNED DEFAULT '0',                
	`uid` INT(12) UNSIGNED DEFAULT NULL,               
	`comments` INT(6) DEFAULT NULL,                    
	`video_tags` VARCHAR(255) DEFAULT NULL, 
	PRIMARY KEY (`id`)                              
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `vidshop_video_downloads` (                  
	`id` INT(17) UNSIGNED NOT NULL AUTO_INCREMENT,  
	`vid` INT(13) UNSIGNED DEFAULT '0',             
	`uid` INT(12) UNSIGNED DEFAULT '0',
	`download` VARCHAR(255) DEFAULT NULL,           
	`ip` VARCHAR(16) DEFAULT NULL,                  
	`addy` VARCHAR(255) DEFAULT NULL,               
	`downloads` INT(7) UNSIGNED DEFAULT '0',        
	`key` VARCHAR(32) DEFAULT NULL,
	`created` INT(14) UNSIGNED DEFAULT '0',         
	PRIMARY KEY (`id`)                              
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE `vidshop_video_cart_items` (     
	`hid` INT(13) UNSIGNED NOT NULL,    
	`vid` INT(13) UNSIGNED NOT NULL     
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `vidshop_video_cart` (                       
	`id` INT(13) UNSIGNED NOT NULL AUTO_INCREMENT,  
	`uid` INT(12) UNSIGNED DEFAULT '0',             
	`created` INT(12) UNSIGNED DEFAULT '0',
	`ip` VARCHAR(16) DEFAULT NULL,                  
	`addy` VARCHAR(255) DEFAULT NULL,               
	`key` VARCHAR(32) DEFAULT NULL,                 
	`items` INT(6) UNSIGNED DEFAULT '0',            
	PRIMARY KEY (`id`)                              
) ENGINE=MYISAM DEFAULT CHARSET=utf8;