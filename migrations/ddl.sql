create table IF NOT EXISTS Authors
(
  AuthorID int auto_increment
    primary key,
  AuthorName varchar(30) null
);

create table IF NOT EXISTS Books
(
  BookID int auto_increment
    primary key,
  BookName varchar(100) null
);

create table IF NOT EXISTS Product
(
  maker varchar(20) null,
  model varchar(20) not null,
  type varchar(20) null,
  constraint Product_model_uindex
    unique (model)
);

alter table Product
  add primary key (model);

create table IF NOT EXISTS Laptop
(
  code tinyint not null
    primary key,
  model varchar(20) null,
  speed int(20) null,
  ram int(20) null,
  hd int(20) null,
  price int(20) null,
  constraint Laptop_Product_model_fk
    foreign key (model) references Product (model)
);

create table IF NOT EXISTS PC
(
  code tinyint not null
    primary key,
  model varchar(20) not null,
  speed int(20) null,
  ram int(20) null,
  hd int(20) null,
  cd varchar(20) null,
  price int(20) null,
  constraint PC_model_uindex
    unique (model),
  constraint PC_Product_model_fk
    foreign key (model) references Product (model)
);

create table IF NOT EXISTS Printer
(
  code tinyint not null
    primary key,
  model varchar(20) null,
  color varchar(20) null,
  type varchar(20) null,
  price int(20) null,
  constraint Printer_Product_model_fk
    foreign key (model) references Product (model)
);

create table IF NOT EXISTS Ru_city_coordinats
(
  city_name varchar(50) null,
  x_cor varchar(50) null,
  y_cor varchar(50) null
);

create table IF NOT EXISTS authorIDtoBookID
(
  BookID int null,
  AuthorID int null
);

create table IF NOT EXISTS city
(
  city_id int(11) unsigned auto_increment
    primary key,
  country_id int(11) unsigned default 0 not null,
  region_id int unsigned default 0 not null,
  name varchar(128) default '' not null
)
  engine=MyISAM;

create index country_id
  on city (country_id);

create index region_id
  on city (region_id);

create table IF NOT EXISTS country
(
  country_id int(11) unsigned auto_increment
    primary key,
  city_id int default 0 not null,
  name varchar(128) default '' not null
)
  engine=MyISAM;

create index city_id
  on country (city_id);

create table IF NOT EXISTS files
(
  id int auto_increment
    primary key,
  name varchar(50) default '' not null,
  type varchar(25) default '' not null,
  size varchar(25) default '' not null,
  source longblob not null
);

create table IF NOT EXISTS geo_table
(
  city varchar(40) charset utf8 not null,
  id int not null
)
  charset=armscii8;

create table IF NOT EXISTS postbacktable
(
  id int auto_increment
    primary key,
  url varchar(10000) null,
  sended int default 0 null
);

create table IF NOT EXISTS postbacktable_test
(
  id int auto_increment
    primary key,
  url varchar(4369) null,
  sended int default 0 null
)
  comment 'for tests';

create table IF NOT EXISTS region
(
  region_id int unsigned auto_increment
    primary key,
  country_id int unsigned default 0 not null,
  city_id int unsigned default 0 not null,
  name varchar(64) default '' not null
)
  engine=MyISAM;

create index city_id
  on region (city_id);

create index country_id
  on region (country_id);

