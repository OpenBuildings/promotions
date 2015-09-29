DROP TABLE IF EXISTS `purchases`;
CREATE TABLE `purchases` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `creator_id` INT(11) UNSIGNED NOT NULL,
  `promo_code_id` INT(11) UNSIGNED DEFAULT NULL,
  `number` VARCHAR(40) NOT NULL,
  `promo_code_text` VARCHAR(40) NOT NULL,
  `currency` VARCHAR(3) NOT NULL,
  `monetary` TEXT,
  `is_frozen` INT(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_deleted` INT(1) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY  (`id`),
  KEY `fk_user_id` (`creator_id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `brand_purchases`;
CREATE TABLE `brand_purchases` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `number` VARCHAR(40) NOT NULL,
  `brand_id` INT(10) UNSIGNED NULL,
  `purchase_id` INT(10) UNSIGNED NULL,
  `is_frozen` INT(1) UNSIGNED NOT NULL,
  `is_deleted` INT(1) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `brand_refunds`;
CREATE TABLE `brand_refunds` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `brand_purchase_id` INT(10) UNSIGNED NULL,
  `created_at` DATETIME,
  `raw_response` TEXT,
  `reason` TEXT,
  `is_deleted` INT(1) UNSIGNED NOT NULL,
  `status` VARCHAR(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `brand_refund_items`;
CREATE TABLE `brand_refund_items` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `brand_refund_id` INT(10) UNSIGNED NULL,
  `purchase_item_id` INT(10) UNSIGNED NULL,
  `amount` DECIMAL(10,2) NULL,
  `is_deleted` INT(1) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_id` INT(10) UNSIGNED NULL,
  `payment_id` VARCHAR(255) NOT NULL,
  `model` VARCHAR(20) NOT NULL,
  `status` VARCHAR(20) NOT NULL,
  `raw_response` TEXT,
  `is_deleted` INT(1) UNSIGNED NOT NULL,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `purchase_items`;
CREATE TABLE `purchase_items` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `brand_purchase_id` INT(10) UNSIGNED NULL,
  `reference_id` INT(10) UNSIGNED NULL,
  `reference_model` VARCHAR(40) NULL,
  `price` DECIMAL(10,2) NULL,
  `quantity` INT(11) NULL,
  `model` VARCHAR(255) NULL,
  `is_payable` INT(1) UNSIGNED NOT NULL,
  `is_discount` INT(1) UNSIGNED NOT NULL,
  `is_frozen` INT(1) UNSIGNED NOT NULL,
  `is_deleted` INT(1) UNSIGNED NOT NULL,
  `created_at` DATETIME,
  `updated_at` DATETIME,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(254) NOT NULL,
  `password` VARCHAR(64) NOT NULL,
  `logins` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `last_login` INT(10) UNSIGNED,
  `facebook_uid` VARCHAR(100),
  `twitter_uid` VARCHAR(100),
  `last_login_ip` VARCHAR(40),
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(254) NOT NULL,
  `currency` VARCHAR(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(254) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(3) NOT NULL,
  `brand_id` INT(10) UNSIGNED NULL,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `variations`;
CREATE TABLE `variations` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(254) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `product_id` INT(10) UNSIGNED NULL,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `promotions`;
CREATE TABLE `promotions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `priority` int(11) DEFAULT 0,
  `model` varchar(100) DEFAULT NULL,
  `requirement` varchar(255) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `description` mediumtext,
  `created_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `promo_codes`;
CREATE TABLE `promo_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `promotion_id` int(11) DEFAULT NULL,
  `origin` varchar(255) DEFAULT NULL,
  `allow_multiple` INT(1) DEFAULT 0 NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

# Dump of table payments
# ------------------------------------------------------------

INSERT INTO `payments` (`id`, `purchase_id`, `payment_id`, `model`, `status`, `raw_response`, `is_deleted`)
VALUES
  (1,1,'11111','payment_emp','paid','{"order_id":"5580812","order_total":"400.00","order_datetime":"2013-08-13 15:04:37","order_status":"Paid","cart":{"item":[{"id":"5657022","code":"1","name":"Chair","description":{},"qty":"1","digital":"0","discount":"0","predefined":"0","unit_price":"200.00"},{"id":"5657032","code":2,"name":"Rug","description":{},"qty":"1","digital":"0","discount":"0","predefined":"0","unit_price":"200.00"}]},"transaction":{"type":"sale","response":"A","response_code":"0","response_text":"approved","trans_id":"1078663342","account_id":"635172"}}',0);

# Dump of table products
# ------------------------------------------------------------

INSERT INTO `products` (`id`, `name`, `price`, `currency`, `brand_id`)
VALUES
  (1,'Chair',290.40,'GBP',1),
  (2,'Rug',30.00,'GBP',1),
  (3,'Matrass',130.99,'EUR',1);

# Dump of table purchases
# ------------------------------------------------------------

INSERT INTO `purchases` (`id`, `creator_id`, `number`, `currency`, `monetary`, `is_frozen`, `is_deleted`)
VALUES
  (1,1,'CNV7IC','EUR','O:31:\"OpenBuildings\\Monetary\\Monetary\":3:{s:20:\"\0*\0_default_currency\";C:33:\"OpenBuildings\\Monetary\\Source_ECB\":728:{a:31:{s:3:\"USD\";s:6:\"1.1170\";s:3:\"JPY\";s:6:\"134.13\";s:3:\"BGN\";s:6:\"1.9558\";s:3:\"CZK\";s:6:\"27.220\";s:3:\"DKK\";s:6:\"7.4597\";s:3:\"GBP\";s:7:\"0.73520\";s:3:\"HUF\";s:6:\"315.43\";s:3:\"PLN\";s:6:\"4.2343\";s:3:\"RON\";s:6:\"4.4173\";s:3:\"SEK\";s:6:\"9.4527\";s:3:\"CHF\";s:6:\"1.0941\";s:3:\"NOK\";s:6:\"9.5820\";s:3:\"HRK\";s:6:\"7.6360\";s:3:\"RUB\";s:7:\"73.3737\";s:3:\"TRY\";s:6:\"3.4137\";s:3:\"AUD\";s:6:\"1.5967\";s:3:\"BRL\";s:6:\"4.4787\";s:3:\"CAD\";s:6:\"1.4921\";s:3:\"CNY\";s:6:\"7.1099\";s:3:\"HKD\";s:6:\"8.6569\";s:3:\"IDR\";s:8:\"16419.85\";s:3:\"ILS\";s:6:\"4.4024\";s:3:\"INR\";s:7:\"73.8391\";s:3:\"KRW\";s:7:\"1334.11\";s:3:\"MXN\";s:7:\"19.0756\";s:3:\"MYR\";s:6:\"4.9410\";s:3:\"NZD\";s:6:\"1.7585\";s:3:\"PHP\";s:6:\"52.271\";s:3:\"SGD\";s:6:\"1.5960\";s:3:\"THB\";s:6:\"40.603\";s:3:\"ZAR\";s:7:\"15.6728\";}}s:10:\"\0*\0_source\";N;s:13:\"\0*\0_precision\";i:2;}',1,0),
  (2,1,'AAV7IC','GBP','',0,0);

# Dump of table brand_purchases
# ------------------------------------------------------------

INSERT INTO `brand_purchases` (`id`, `number`, `brand_id`, `purchase_id`, `is_deleted`)
VALUES
  (1,'3S2GJG',1,1,0),
  (2,'AA2GJG',1,2,0);

# Dump of table purchase_items
# ------------------------------------------------------------

INSERT INTO `purchase_items` (`id`, `brand_purchase_id`, `reference_id`, `reference_model`, `price`, `quantity`, `model`, `is_payable`, `is_discount`, `is_deleted`)
VALUES
  (1,1,1,'product',200.00,1,'purchase_item_product',1,0,0),
  (2,1,1,'variation',200.00,1,'purchase_item_product',1,0,0),
  (3,2,1,'product',NULL,1,'purchase_item_product',1,0,0);

# Dump of table brands
# ------------------------------------------------------------

INSERT INTO `brands` (`id`, `name`)
VALUES
  (1,'Example Brand'),
  (2,'Empty Brand');

# Dump of table users
# ------------------------------------------------------------

INSERT INTO `users` (`id`, `email`, `password`, `logins`, `last_login`, `facebook_uid`, `twitter_uid`, `last_login_ip`)
VALUES
  (1,'admin@example.com','f02c9f1f724ebcf9db6784175cb6bd82663380a5f8bd78c57ad20d5dfd953f15',5,1374320224,'facebook-test','','10.20.10.1');


# Dump of table variations
# ------------------------------------------------------------
INSERT INTO `variations` (`id`, `name`, `price`, `product_id`)
VALUES
  (1,'Red',295.40,1),
  (2,'Green',298.90,1);


INSERT INTO `promotions` (`id`, `name`, `model`, `requirement`, `amount`, `currency`, `description`, `created_at`, `expires_at`)
VALUES
  (1, 'Discount Promotion', 'promotion_promocode_percent', NULL, 0.05, NULL, '5% discount of the items price for orders above 50GBP', '2013-08-01 12:00:00', NULL),
  (2, 'Discount Promotion', 'promotion_promocode_giftcard', 200, 10, 'GBP', '10% discount of the items price for orders above 200GBP', '2013-08-15 12:00:00', NULL);


INSERT INTO `promo_codes` (`id`, `code`, `promotion_id`, `origin`, `allow_multiple`, `created_at`, `expires_at`)
VALUES
  (1, '1ZMA56', 1, 'successful-purchase', 0, '2013-07-10 12:13:50', NULL),
  (2, '621ZWM', 2, 'successful-purchase', 0, '2013-07-10 12:13:51', NULL),
  (3, '8BZD45', 2, 'successful-purchase', 1, '2013-08-16 14:27:18', NULL);
