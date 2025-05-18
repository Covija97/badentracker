-- Active: 1745596725282@@127.0.0.1@3306@badentracker
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

SELECT
    prog.prog_id,
    prog.prog_date,
    prog.prog_time,
    prog.prog_coord,
    prog.prog_place,
    prog.prog_child_N,
    prog.grp_id,
    grps.grp_name,
    GROUP_CONCAT(DISTINCT rama.rama_name SEPARATOR '<br>') AS prog_rama
FROM prog
LEFT JOIN grps ON prog.grp_id = grps.grp_id
LEFT JOIN prog_rama ON prog.prog_id = prog_rama.prog_id
LEFT JOIN rama ON prog_rama.rama_id = rama.rama_id

GROUP BY prog.prog_id, prog.prog_date, prog.prog_time, prog.prog_coord, prog.prog_place, prog.prog_child_N, prog.grp_id, grps.grp_name
