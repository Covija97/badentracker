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
    FOREIGN KEY (grp_id) REFERENCES grps(grp_id)
);


DROP TABLE IF EXISTS prog_rama;
CREATE TABLE IF NOT EXISTS prog_rama(
    prog_id     int         NOT NULL                           COMMENT 'Foreign Key to prog',
    rama_id     int         NOT NULL                           COMMENT 'Foreign Key to rama',
    PRIMARY KEY (prog_id, rama_id),
    FOREIGN KEY (prog_id) REFERENCES prog(prog_id) ON DELETE CASCADE,
    FOREIGN KEY (rama_id) REFERENCES rama(rama_id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS prog_act;
CREATE TABLE IF NOT EXISTS prog_act(
    prog_id     int         NOT NULL                           COMMENT 'Foreign Key to prog',
    act_id      int         NOT NULL                           COMMENT 'Foreign Key to act',
    act_order   int         NOT NULL                           COMMENT 'Order of the act',
    PRIMARY KEY (prog_id, act_id),
    FOREIGN KEY (prog_id) REFERENCES prog(prog_id) ON DELETE CASCADE,
    FOREIGN KEY (act_id) REFERENCES act(act_id) ON DELETE CASCADE
);


INSERT INTO rama (rama_name) VALUES
('Castores'),
('Lobatos'),
('Rangers'),
('Pioneros'),
('Rutas');


INSERT INTO grps (grp_name, grp_logo) VALUES
('Grupo Scout 1', 'logo1.png'),
('Grupo Scout 2', 'logo2.png'),
('Grupo Scout 3', 'logo3.png'),
('Grupo Scout 4', 'logo4.png'),
('Grupo Scout 5', 'logo5.png');


INSERT into prog (prog_date, prog_time, prog_coord, prog_place, prog_child_N, grp_id) VALUES
('2023-10-01', '10:00:00', 'Juan Pérez', 'Parque Central', 20, 1),
('2023-10-08', '10:00:00', 'María López', 'Plaza de la Ciudad', 15, 2),
('2023-10-15', '10:00:00', 'Carlos García', 'Bosque de la Montaña', 25, 1),
('2023-10-22', '10:00:00', 'Ana Martínez', 'Río Azul', 30, 2),
('2023-10-29', '10:00:00', 'Luis Fernández', 'Cerro Verde', 18, 1);

INSERT into prog_rama (prog_id, rama_id) VALUES
(18, 1),
(19, 2),
(19, 3),
(21, 2),
(22, 5);