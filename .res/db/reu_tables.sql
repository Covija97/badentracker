-- Active: 1745596725282@@127.0.0.1@3306@badentracker
/* ----------------------------- Tablas CRUD Reunión ----------------------------- */
DROP TABLE IF EXISTS prog;
CREATE TABLE IF NOT EXISTS prog(
    prog_id     int         NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    prog_date   date        NOT NULL                           COMMENT 'Date of the meeting',
    prog_time   time(0)     NOT NULL                           COMMENT 'Time of the meeting',
);