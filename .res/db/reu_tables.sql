-- Active: 1745596725282@@127.0.0.1@3306@badentracker
/* ----------------------------- Tablas CRUD Reunión ----------------------------- */


DROP TABLE IF EXISTS rama;
CREATE TABLE IF NOT EXISTS rama(
    rama_id     int         NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    rama_name   varchar(50) NOT NULL                           COMMENT 'Name of the branch'
);

DROP TABLE IF EXISTS grps;
CREATE TABLE IF NOT EXISTS grps(
    grp_id      int         NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    grp_name    varchar(50) NOT NULL                           COMMENT 'Name of the group',
    grp_logo    LONGBLOB     NOT NULL                           COMMENT 'Logo of the group'
);

DROP TABLE IF EXISTS prog;
CREATE TABLE IF NOT EXISTS prog(
    prog_id     int         NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    prog_date   date        NOT NULL                            COMMENT 'Date of the meeting',
    prog_time   time        NOT NULL                            COMMENT 'meeting start time',
    prog_coord   varchar(50) NOT NULL                           COMMENT 'Coordinator of the meeting',
    prog_place  varchar(50) NOT NULL                           COMMENT 'Place of the meeting',
    prog_child_N int NOT NULL                           COMMENT 'Number of children in the meeting',
    grp_id      int NOT NULL                           COMMENT 'Foreign Key to grps',
    rama_id     int NOT NULL                           COMMENT 'Foreign Key to rama',
    FOREIGN KEY (grp_id) REFERENCES grps(grp_id),
    FOREIGN KEY (rama_id) REFERENCES rama(rama_id)
);

DROP TABLE IF EXISTS prog_act;
CREATE TABLE IF NOT EXISTS prog_act(
    prog_id     int         NOT NULL                           COMMENT 'Foreign Key to prog',
    act_id      int         NOT NULL                           COMMENT 'Foreign Key to act',
    act_order   int         NOT NULL                           COMMENT 'Order of the act',
    act_respon VARCHAR(50) NOT NULL                           COMMENT 'Responsible for the act',
    act_comment LONGBLOB NOT NULL                           COMMENT 'Comment for the act',
    PRIMARY KEY (prog_id, act_id),
    FOREIGN KEY (prog_id) REFERENCES prog(prog_id) ON DELETE CASCADE,
    FOREIGN KEY (act_id) REFERENCES act(act_id) ON DELETE CASCADE
);
