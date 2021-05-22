-- auto-generated definition
create table logs
(
    id            int auto_increment
        primary key,
    ip            varchar(255) null,
    `date-time`   varchar(255) null,
    url           varchar(255) null,
    `user-agent`  varchar(255) null,
    `oper-system` varchar(255) null,
    architecture  varchar(255) null,
    browser       varchar(255) null
);

