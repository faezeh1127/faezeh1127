USE rahmahd_db;

-- admin_users
CREATE TABLE IF NOT EXISTS admin_users (
  username          VARCHAR(16) UNIQUE,
  password          VARCHAR(64),
  status            BOOLEAN DEFAULT 1,
  PRIMARY KEY (username)
);

-- manager_users
CREATE TABLE IF NOT EXISTS manager_users (
  id                INT(11) UNIQUE AUTO_INCREMENT,
  username          VARCHAR(16) UNIQUE,
  password          VARCHAR(64),
  kd_id             INT(11),
  full_name         VARCHAR(64),
  PRIMARY KEY (id)
);

-- request_kd
CREATE TABLE IF NOT EXISTS request_kd (
  id                INT(11) UNIQUE AUTO_INCREMENT,
  manager_user_id   INT(11),
  kd_name           VARCHAR(64),
  address           TEXT,
  status_code       TINYINT(3),
  PRIMARY KEY (id)
);

-- kindergartens
CREATE TABLE IF NOT EXISTS kindergartens (
  id                INT(11) UNIQUE AUTO_INCREMENT,
  name              VARCHAR(64) UNIQUE,
  location          POINT,
  rate              FLOAT,
  opening_date      DATE,
  work_hour_start   TIME,
  work_hour_end     TIME,
  address           TEXT UNIQUE,
  telephone         VARCHAR(11),
  capacity          TINYINT(3) DEFAULT 0,
  max_capacity      TINYINT(3),
  PRIMARY KEY (id)
);

-- kids
CREATE TABLE IF NOT EXISTS kids (
  id                INT(11) UNIQUE AUTO_INCREMENT,
  first_name        VARCHAR(32),
  last_name         VARCHAR(32),
  age               TINYINT(3),
  telephone         VARCHAR(11),
  gender            BOOLEAN,
  national_id       VARCHAR(10),
  kd_id             INT(11),
  PRIMARY KEY (id)
);

-- payment_factors
CREATE TABLE IF NOT EXISTS payment_factors (
  id                INT(11) UNIQUE AUTO_INCREMENT,
  kd_id             INT(11),
  title             VARCHAR(128),
  creation_date     DATE,
  value_total       BIGINT(11),
  discount_total    BIGINT(11),
  final_value       BIGINT(11),
  kid_id            INT(11),
  status            TINYINT(3),
  PRIMARY KEY (id)
);

-- kd_images
CREATE TABLE IF NOT EXISTS kd_images (
  kd_id             INT(11),
  first_name        VARCHAR(256) UNIQUE
);

-- manager_user_login
CREATE TABLE IF NOT EXISTS manager_user_login (
  id                INT(11) UNIQUE,
  token             VARCHAR(32) UNIQUE,
  expire            DATETIME
  PRIMARY KEY (id)
);

-- admin_user_login
CREATE TABLE IF NOT EXISTS admin_user_login (
  username          VARCHAR(16) UNIQUE,
  token             VARCHAR(32) UNIQUE,
  expire            DATETIME
  PRIMARY KEY (username)
);
