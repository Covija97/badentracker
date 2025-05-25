-- Active: 1745596725282@@127.0.0.1@3306@badentracker
/* ----------------------------- Tablas CRUD Reunión ----------------------------- */

INSERT INTO rama (rama_name) VALUES
('Grupo'),
('Castores'),
('Lobatos'),
('Rangers'),
('Pioneros'),
('Rutas');


INSERT INTO grps (grp_name) VALUES
('JEYMA');


INSERT into prog (prog_date, prog_time, prog_coord, prog_place, prog_child_N, grp_id, rama_id) VALUES
('2023-10-01', '10:00:00', 'Jorge', 'Casa de Jorge', 5, 1, 2),
('2023-10-02', '11:00:00', 'Jorge', 'Casa de Jorge', 5, 1, 2),
('2023-10-03', '12:00:00', 'Jorge', 'Casa de Jorge', 5, 1, 2),
('2023-10-04', '13:00:00', 'Jorge', 'Casa de Jorge', 5, 1, 2),
('2023-10-05', '14:00:00', 'Jorge', 'Casa de Jorge', 5, 1, 2),
('2023-10-06', '15:00:00', 'Jorge', 'Casa de Jorge', 5, 1, 2),
('2023-10-07', '16:00:00', 'Jorge', 'Casa de Jorge', 5, 1, 2),
('2023-10-08', '17:00:00', 'Jorge', 'Casa de Jorge', 5, 1, 2),
('2023-10-09', '18:00:00', 'Jorge', 'Casa de Jorge', 5, 1, 2),
('2023-10-10', '19:00:00', 'Jorge', 'Casa de Jorge', 5, 1, 2);
