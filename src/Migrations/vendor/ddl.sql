create schema sk8kilay_test collate utf8_general_ci;

create table cityads_country_russia
(
  id int auto_increment,
  city_name varchar(40) charset utf8 not null,
  cityads_id int not null,
  constraint id
    unique (id)
)
  charset=utf8;

alter table cityads_country_russia
  add primary key (id);

create table cityads_world_region
(
  id int(10) auto_increment,
  region_name varchar(80) not null,
  cityads_id int(10) null,
  constraint cityads_world_region_id_uindex
    unique (id)
);

alter table cityads_world_region
  add primary key (id);

create table cityads_world_region_codes
(
  id int(10) auto_increment,
  region_code varchar(2) not null,
  cityads_id int(10) null,
  constraint cityads_world_region_codes_id_uindex
    unique (id)
);

alter table cityads_world_region_codes
  add primary key (id);

create table postbacktable
(
  id int auto_increment
    primary key,
  url varchar(10000) null,
  sended int default 0 null
);