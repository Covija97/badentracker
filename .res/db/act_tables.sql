-- Active: 1745596725282@@127.0.0.1@3306@badentracker
/* ----------------------------- Tablas CRUD Actividades ----------------------------- */

/* Crear usuario y asignar permisos */
CREATE USER 'bt'@'localhost' IDENTIFIED by 'BadenTracker2025*';
GRANT ALL PRIVILEGES ON badentracker.* TO 'bt'@'localhost';
FLUSH PRIVILEGES;

/* Eliminar tablas */
DROP TABLE IF EXISTS act_mat;
DROP TABLE IF EXISTS act_cat;
DROP TABLE IF EXISTS act_obj;
DROP TABLE IF EXISTS mat;
DROP TABLE IF EXISTS cat;
DROP TABLE IF EXISTS obj;
DROP TABLE IF EXISTS act;

/* Tabla de actividades */
CREATE TABLE IF NOT EXISTS act(  
    act_id      int         NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    act_name    varchar(50) NOT NULL UNIQUE                     COMMENT 'Activity Name',
    act_desc    varchar(255)                                    COMMENT 'Activity Description',
    act_durat   time(0)                                         COMMENT 'Activity Duration'
);

/* Tabla de objetivos */
CREATE TABLE IF NOT EXISTS obj(  
    obj_id      int         NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    obj_name    varchar(50) NOT NULL UNIQUE                     COMMENT 'Objective Name',
    obj_desc    varchar(255)                                    COMMENT 'Objective Description'
);

/* Tabla de categorias de actividades */
CREATE TABLE IF NOT EXISTS cat(  
    cat_id      int         NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    cat_name    varchar(50) NOT NULL UNIQUE                     COMMENT 'Category Name',
    cat_desc    varchar(255)                                    COMMENT 'Category Description'
);

/* Tabla de materiales */
CREATE TABLE IF NOT EXISTS mat(  
    mat_id      int         NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    mat_name    varchar(50) NOT NULL UNIQUE                     COMMENT 'Material Name',
    mat_desc    varchar(255)                                    COMMENT 'Material Description'
);


/* ----------------------------- Tablas intermedias y relaciones ----------------------------- */

/* Tabla entre actividades y objetivos */
CREATE TABLE IF NOT EXISTS act_obj(
    act_id      int         NOT NULL COMMENT 'Foreign Key',
    obj_id      int         NOT NULL COMMENT 'Foreign Key',
    PRIMARY KEY (act_id, obj_id),
    FOREIGN KEY (act_id) REFERENCES act(act_id) ON DELETE CASCADE,
    FOREIGN KEY (obj_id) REFERENCES obj(obj_id) ON DELETE CASCADE
);

/* Tabla entre actividades y categorias */
CREATE TABLE IF NOT EXISTS act_cat(
    act_id      int         NOT NULL COMMENT 'Foreign Key',
    cat_id      int         NOT NULL COMMENT 'Foreign Key',
    PRIMARY KEY (act_id, cat_id),
    FOREIGN KEY (act_id) REFERENCES act(act_id) ON DELETE CASCADE,
    FOREIGN KEY (cat_id) REFERENCES cat(cat_id) ON DELETE CASCADE
);

/* Tabla entre actividades y materiales */
CREATE TABLE IF NOT EXISTS act_mat(
    act_id      int         NOT NULL COMMENT 'Foreign Key',
    mat_id      int         NOT NULL COMMENT 'Foreign Key',
    PRIMARY KEY (act_id, mat_id),
    FOREIGN KEY (act_id) REFERENCES act(act_id) ON DELETE CASCADE,
    FOREIGN KEY (mat_id) REFERENCES mat(mat_id) ON DELETE CASCADE
);