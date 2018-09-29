-- creditcash
CREATE TABLE IF NOT EXISTS `requests` (
  `id`               INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `agree_id`         VARCHAR(64)         NOT NULL DEFAULT '',
  `full_name`        VARCHAR(512)        NOT NULL DEFAULT '',
  `email`            VARCHAR(512)        NOT NULL DEFAULT '',
  `phone`            VARCHAR(32)         NOT NULL DEFAULT '',
  `state_code`       VARCHAR(128)        NOT NULL DEFAULT '',
  `city`             VARCHAR(128)        NOT NULL DEFAULT '',
  `form_id`          VARCHAR(128)        NOT NULL DEFAULT '',
  `employment`       VARCHAR(128)        NOT NULL DEFAULT '',
  `sum`              DECIMAL(12, 2)      NOT NULL DEFAULT 0.00,
  `partner`          VARCHAR(128)        NOT NULL DEFAULT '',
  `resource`         VARCHAR(128)        NOT NULL DEFAULT '',
  `request_id`       VARCHAR(128)        NOT NULL DEFAULT '',
  `confirm_code`     VARCHAR(128)        NOT NULL DEFAULT '',
  `cpa`              VARCHAR(128)        NOT NULL DEFAULT '',
  `approved`         TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `request`          TEXT                         DEFAULT NULL,
  `response`         TEXT                         DEFAULT NULL,
  `confirm_response` TEXT                         DEFAULT NULL,
  `add_time`         TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='requests' AUTO_INCREMENT=1;

-- creditcard
CREATE TABLE IF NOT EXISTS `requests_card` (
  `id`               INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `first_name`       VARCHAR(512)        NOT NULL DEFAULT '',
  `second_name`      VARCHAR(512)        NOT NULL DEFAULT '',
  `phone`            VARCHAR(32)         NOT NULL DEFAULT '',
  `state_code`       VARCHAR(128)        NOT NULL DEFAULT '',
  `employment`       VARCHAR(128)        NOT NULL DEFAULT '',
  `cpa`              VARCHAR(128)        NOT NULL DEFAULT '',
  `confirm_code`     VARCHAR(128)        NOT NULL DEFAULT '',
  `request_id`       VARCHAR(128)        NOT NULL DEFAULT '',
  `approved`         TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `request`          TEXT                         DEFAULT NULL,
  `response`         TEXT                         DEFAULT NULL,
  `add_time`         TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='requests_card' AUTO_INCREMENT=1;

-- site_settings
CREATE TABLE IF NOT EXISTS `site_settings` (
  `key`       VARCHAR(128)  NOT NULL DEFAULT '',
  `value`     TEXT                   DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='site_settings';
