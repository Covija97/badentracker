-- Active: 1745596725282@@127.0.0.1@3306@badentracker
/* ----------------------------- Tablas principales ----------------------------- */

/* Tabla de actividades */
DROP TABLE IF EXISTS act;

CREATE TABLE IF NOT EXISTS act(  
    act_id      int         NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    act_name    varchar(50) NOT NULL UNIQUE                     COMMENT 'Activity Name',
    act_desc    varchar(255)                                    COMMENT 'Activity Description',
    act_durat   time(0)                                         COMMENT 'Activity Duration'
);

INSERT INTO act(act_name, act_desc, act_durat) VALUES
('Actividad 6', 'Descripción de la actividad 6', '00:20'),
('Actividad 7', 'Descripción de la actividad 7', '00:50'),
('Actividad 8', 'Descripción de la actividad 8', '01:10'),
('Actividad 9', 'Descripción de la actividad 9', '00:40'),
('Actividad 10', 'Descripción de la actividad 10', '00:25'),
('Actividad 11', 'Descripción de la actividad 11', '00:55'),
('Actividad 12', 'Descripción de la actividad 12', '01:05'),
('Actividad 13', 'Descripción de la actividad 13', '00:35'),
('Actividad 14', 'Descripción de la actividad 14', '00:15'),
('Actividad 15', 'Descripción de la actividad 15', '00:45'),
('Actividad 16', 'Descripción de la actividad 16', '01:20'),
('Actividad 17', 'Descripción de la actividad 17', '00:30'),
('Actividad 18', 'Descripción de la actividad 18', '00:50'),
('Actividad 19', 'Descripción de la actividad 19', '01:10'),
('Actividad 20', 'Descripción de la actividad 20', '00:40'),
('Actividad 21', 'Descripción de la actividad 21', '00:25'),
('Actividad 22', 'Descripción de la actividad 22', '00:55'),
('Actividad 23', 'Descripción de la actividad 23', '01:05'),
('Actividad 24', 'Descripción de la actividad 24', '00:35'),
('Actividad 25', 'Descripción de la actividad 25', '00:15'),
('Actividad 26', 'Descripción de la actividad 26', '00:45'),
('Actividad 27', 'Descripción de la actividad 27', '01:20'),
('Actividad 28', 'Descripción de la actividad 28', '00:30'),
('Actividad 29', 'Descripción de la actividad 29', '00:50'),
('Actividad 30', 'Descripción de la actividad 30', '01:10');

/* Tabla de objetivos */
DROP TABLE IF EXISTS obj;

CREATE TABLE IF NOT EXISTS obj(  
    obj_id      int         NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    obj_name    varchar(50) NOT NULL UNIQUE                     COMMENT 'Objective Name',
    obj_desc    varchar(255)                                    COMMENT 'Objective Description'
);

INSERT INTO obj(obj_name, obj_desc) VALUES
('Objetivo 1', 'Descripción del objetivo 1'),
('Objetivo 2', 'Descripción del objetivo 2'),
('Objetivo 3', 'Descripción del objetivo 3'),
('Objetivo 4', 'Descripción del objetivo 4'),
('Objetivo 5', 'Descripción del objetivo 5');

/* Tabla de categorias de actividades */
DROP TABLE IF EXISTS cat;

CREATE TABLE IF NOT EXISTS cat(  
    cat_id      int         NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    cat_name    varchar(50) NOT NULL UNIQUE                     COMMENT 'Category Name',
    cat_desc    varchar(255)                                    COMMENT 'Category Description'
);

INSERT INTO cat(cat_name, cat_desc) VALUES
('Categoría 1', 'Descripción de la categoría 1'),
('Categoría 2', 'Descripción de la categoría 2'),
('Categoría 3', 'Descripción de la categoría 3'),
('Categoría 4', 'Descripción de la categoría 4'),
('Categoría 5', 'Descripción de la categoría 5');

/* Tabla de materiales */
DROP TABLE IF EXISTS mat;

CREATE TABLE IF NOT EXISTS mat(  
    mat_id      int         NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    mat_name    varchar(50) NOT NULL UNIQUE                     COMMENT 'Material Name',
    mat_desc    varchar(255)                                    COMMENT 'Material Description'
);

INSERT INTO mat(mat_name, mat_desc) VALUES
('Material 1', 'Descripción del material 1'),
('Material 2', 'Descripción del material 2'),
('Material 3', 'Descripción del material 3'),
('Material 4', 'Descripción del material 4'),
('Material 5', 'Descripción del material 5');


/* ----------------------------- Tablas intermedias ----------------------------- */

/* Tabla entre actividades y objetivos */
DROP TABLE IF EXISTS act_obj;

CREATE TABLE IF NOT EXISTS act_obj(
    act_id      int         NOT NULL COMMENT 'Foreign Key',
    obj_id      int         NOT NULL COMMENT 'Foreign Key',
    PRIMARY KEY (act_id, obj_id),
    FOREIGN KEY (act_id) REFERENCES act(act_id) ON DELETE CASCADE,
    FOREIGN KEY (obj_id) REFERENCES obj(obj_id) ON DELETE CASCADE
);

INSERT INTO act_obj(act_id, obj_id) VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 4),
(4, 5),
(5, 1);

/* Tabla entre actividades y categorias */

DROP TABLE IF EXISTS act_cat;

CREATE TABLE IF NOT EXISTS act_cat(
    act_id      int         NOT NULL COMMENT 'Foreign Key',
    cat_id      int         NOT NULL COMMENT 'Foreign Key',
    PRIMARY KEY (act_id, cat_id),
    FOREIGN KEY (act_id) REFERENCES act(act_id) ON DELETE CASCADE,
    FOREIGN KEY (cat_id) REFERENCES cat(cat_id) ON DELETE CASCADE
);

INSERT INTO act_cat(act_id, cat_id) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5);

/* Tabla entre actividades y materiales */

DROP TABLE IF EXISTS act_mat;

CREATE TABLE IF NOT EXISTS act_mat(
    act_id      int         NOT NULL COMMENT 'Foreign Key',
    mat_id      int         NOT NULL COMMENT 'Foreign Key',
    PRIMARY KEY (act_id, mat_id),
    FOREIGN KEY (act_id) REFERENCES act(act_id) ON DELETE CASCADE,
    FOREIGN KEY (mat_id) REFERENCES mat(mat_id) ON DELETE CASCADE
);

INSERT INTO act_mat(act_id, mat_id) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5);

/* ----------------------------- Prueba de consultas ----------------------------- */

SELECT * FROM act;

SELECT 
    act.act_name,
    act.act_desc,
    act.act_durat,
    GROUP_CONCAT(DISTINCT obj.obj_name SEPARATOR '<br>') AS act_objs,
    GROUP_CONCAT(DISTINCT cat.cat_name SEPARATOR '<br>') AS act_cats,
    GROUP_CONCAT(DISTINCT mat.mat_name SEPARATOR '<br>') AS act_mats
FROM act
LEFT JOIN act_obj ON act.act_id = act_obj.act_id
LEFT JOIN obj ON act_obj.obj_id = obj.obj_id

LEFT JOIN act_cat ON act.act_id = act_cat.act_id
LEFT JOIN cat ON act_cat.cat_id = cat.cat_id

LEFT JOIN act_mat ON act.act_id = act_mat.act_id
LEFT JOIN mat ON act_mat.mat_id = mat.mat_id

GROUP BY act.act_id, act.act_name, act.act_desc, act.act_durat;


