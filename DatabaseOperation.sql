drop database if exists test;
create database test;
use test;
drop table if exists user_msg;
create table user_msg(
user_name char(20) primary key,
user_pwd char(20) not null,
location char(50) not null
)engine=InnoDB default charset=gbk;