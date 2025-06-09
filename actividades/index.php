<?php

$page = "act";
$title = "Actividades";

require "../.res/funct/funct.php";
include "../.res/templates/header.php";

$sql = "
SELECT
    act.act_id,
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
";

$query = linkDB() -> query($sql);

?>
<main>
    <a class="but align-left" href="new" with="50px" aling="left" title="Nueva Actividad">
        <svg width="400" height="400" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
        <path
            d="m 2,6 h 8"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
            id="path1" />
        <path
            d="M 6,10 V 2"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
            id="path2" />
        </svg>
    </a>

    <input type="text" id="searchInput" placeholder="Buscar dentro de actividades..." class="search-input">

    <br><br>
    
    <table class="table-main">
        <tr>
            <th data-sortable="true" width="">Actividad</th>
            <th data-sortable="true">Duración</th>
            <th data-sortable="true">
                <a class="but2 align-left" href="objetivos" title="Ir a objetivos"> Objetivos </a>
            </th>
            <th data-sortable="true">
                <a class="but2 align-left" href="categorias" title="Ir a categorias"> Categorias </a>
            </th>
            <th data-sortable="true">
                <a class="but2 align-left" href="materiales" title="Ir a materiales"> Materiales </a>
            </th>
        </tr>
        <?php if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                echo "<tr>";
                echo
                    "<td>
                        <a class=but2  href='actividad?id=" . $row["act_id"] . "' title='Editar " . $row["act_name"] . "'>" .
                            $row["act_name"] ."
                        </a>
                    </td>";
                echo "<td>" . $row["act_durat"] . "</td>";
                echo "<td>" . $row["act_objs"] . "</td>";
                echo "<td>" . $row["act_cats"] . "</td>";
                echo "<td>" . $row["act_mats"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No hay actividades disponibles.</td></tr>";
        } ?>        
    </table>

</main>
<?php
include "../.res/templates/footer.php";
?>