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
            <th data-sortable="true">Actividad</th>
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
            <th></th>
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
                ?>
                <td>
                    <a class="but align-left" href="actividad/delete.php?id=<?php echo $row["act_id"];?> " title="Borrar <?php echo $row["act_name"] ?>" onclick="return confirm('¿Estás seguro de que deseas borrar esta actividad?');">
                        <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 5.97998C17.67 5.64998 14.32 5.47998 10.98 5.47998C9 5.47998 7.02 5.57998 5.04 5.77998L3 5.97998"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8.5 4.97L8.72 3.66C8.88 2.71 9 2 10.69 2H13.31C15 2 15.13 2.75 15.28 3.67L15.5 4.97"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18.85 9.14001L18.2 19.21C18.09 20.78 18 22 15.21 22H8.79002C6.00002 22 5.91002 20.78 5.80002 19.21L5.15002 9.14001"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10.33 16.5H13.66"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9.5 12.5H14.5"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </td>
                <?php
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