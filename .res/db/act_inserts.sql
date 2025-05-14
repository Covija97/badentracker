-- Active: 1745596725282@@127.0.0.1@3306@badentracker
/* ----------------------------- Tablas CRUD Actividades ----------------------------- */

/* Tabla de actividades */
INSERT INTO act (act_name, act_desc, act_durat) VALUES
('Construcción de refugio', 'Construcción de refugios con elementos naturales', '02:00:00'),
('Nudos y amarres', 'Práctica de nudos básicos y amarres', '01:30:00'),
('Carrera de orientación', 'Competencia con brújula y mapa', '01:45:00'),
('Taller de primeros auxilios', 'Aprendizaje básico de primeros auxilios', '02:30:00'),
('Gymkhana scout', 'Juegos en estaciones por equipos', '01:00:00'),
('Manualidades con reciclaje', 'Crear objetos útiles con materiales reciclados', '01:30:00'),
('Búsqueda del tesoro', 'Actividad de exploración con pistas', '02:00:00'),
('Juego del paracaídas', 'Juego cooperativo con paracaídas de tela', '00:45:00'),
('Construcción de brújula casera', 'Fabricar una brújula con imán y aguja', '01:00:00'),
('Fútbol scout', 'Juego de fútbol entre patrullas', '01:30:00'),
('Senderismo corto', 'Caminata guiada por la montaña', '02:30:00'),
('Fogata nocturna', 'Actividad de fogata con cantos y juegos', '02:00:00'),
('Día sin tecnología', 'Jornada de actividades sin dispositivos electrónicos', '08:00:00'),
('Elaboración de banderines', 'Diseño y creación de banderines de patrulla', '01:00:00'),
('Tiro con arco básico', 'Introducción al tiro con arco', '02:00:00'),
('Simulación de rescate', 'Simulación de emergencia y rescate', '01:30:00'),
('Campamento base', 'Organización del campamento y montaje de carpas', '03:00:00'),
('Juego de confianza', 'Dinámica para fortalecer la confianza', '01:00:00'),
('Mapa mental de valores', 'Reflexión gráfica de los valores scout', '01:15:00'),
('Exploración de flora', 'Identificación de plantas locales', '01:30:00'),
('Cine scout', 'Visualización de documental y debate', '02:00:00'),
('Improvisación teatral', 'Escenificaciones improvisadas', '01:30:00'),
('Juegos de pista', 'Juegos que siguen una serie de pistas', '01:30:00'),
('Competencia de reciclaje', 'Competencia por crear con materiales reciclables', '01:45:00'),
('Taller de liderazgo', 'Dinámicas para fortalecer el liderazgo', '02:00:00'),
('Diseño de insignias', 'Crear insignias representativas del grupo', '01:30:00'),
('El juego del silencio', 'Juego de concentración y atención', '00:30:00'),
('Escultismo histórico', 'Charla interactiva sobre historia scout', '01:30:00'),
('Fotografía de naturaleza', 'Actividad para tomar fotos en el entorno', '01:30:00'),
('Competencia deportiva', 'Juegos deportivos en equipos', '02:00:00');

/* Tabla de objetivos */
INSERT INTO obj (obj_name, obj_desc) VALUES
('Desarrollar liderazgo', 'Fomentar habilidades de liderazgo en los scouts'),
('Mejorar trabajo en equipo', 'Incentivar la cooperación y comunicación'),
('Aprender técnicas de supervivencia', 'Dominar habilidades básicas en la naturaleza'),
('Fomentar la creatividad', 'Estimular la creatividad en los scouts'),
('Desarrollar condición física', 'Promover el ejercicio y la salud física');

/* Tabla de categorias de actividades */
INSERT INTO cat (cat_name, cat_desc) VALUES
('Supervivencia', 'Actividades relacionadas con habilidades de supervivencia'),
('Exploración', 'Actividades de exploración de la naturaleza'),
('Trabajo en equipo', 'Actividades que fomentan el trabajo en grupo'),
('Manualidades', 'Actividades creativas y de construcción manual'),
('Deportes', 'Actividades físicas y deportivas'),
('Reflexión', 'Actividades de reflexión y aprendizaje personal');

/* Tabla de materiales */
INSERT INTO mat (mat_name, mat_desc) VALUES
('Cuerda', 'Cuerda resistente para actividades al aire libre'),
('Tijeras', 'Tijeras de seguridad para manualidades'),
('Mapa', 'Mapa topográfico de la zona'),
('Brújula', 'Brújula para orientación'),
('Botiquín', 'Botiquín básico de primeros auxilios'),
('Linterna', 'Linterna portátil'),
('Cartulina', 'Cartulina de colores para manualidades'),
('Pelota', 'Pelota para juegos o deportes');

/* ----------------------------- Tablas intermedias y relaciones ----------------------------- */

/* Tabla entre actividades y objetivos */
INSERT INTO act_obj (act_id, obj_id) VALUES
(96,13), (97,13), (98,13), (99,11), (100,12), (101,14), (102,12), (103,12), (104,13), (105,15),
(106,15), (107,12), (108,12), (109,14), (110,15), (111,11), (112,13), (113,12), (114,14), (115,13),
(116,11), (117,14), (118,12), (119,14), (120,11), (121,14), (122,12), (123,11), (124,14), (125,15);

/* Tabla entre actividades y categorias */
INSERT INTO act_cat (act_id, cat_id) VALUES
(96,9), (97,9), (98,10), (99,9), (100,11), (101,12), (102,10), (103,11), (104,9), (105,13),
(106,10), (107,11), (108,11), (109,12), (110,13), (111,9), (112,9), (113,11), (114,12), (115,10),
(116,11), (117,12), (118,10), (119,12), (120,11), (121,12), (122,11), (123,11), (124,10), (125,13);

/* Tabla entre actividades y materiales */
INSERT INTO act_mat (act_id, mat_id) VALUES
(96,7), (97,7), (98,9), (98,10), (99,11), (100,14), (101,13), (101,8), (102,9), (103,7),
(104,8), (104,10), (105,14), (106,9), (106,10), (107,12), (108,11), (109,13), (110,7), (110,14),
(111,11), (112,7), (113,7), (114,13), (115,9), (116,12), (117,13), (118,9), (119,13), (120,7),
(121,13), (122,12), (123,12), (124,12), (125,14);