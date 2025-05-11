<?php

$page = "act";
$title = "Objetivos";

require "../../.res/funct/funct.php";
include "../../.res/templates/header.php";

$sql = "
SELECT
    obj.obj_id,
    obj.obj_name,
    obj.obj_desc
FROM obj
";

$query = linkDB() -> query($sql);

?>
<main>
    <a class="but align-left" href="../" title="Volver a Actividades">
        <svg width="400" height="400" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
            d="m 3.0000046,9.88004 h 4.91998 c 1.7,0 3.0800004,-1.38 3.0800004,-3.08 0,-1.7 -1.3800004,-3.07999 -3.0800004,-3.07999 h -6.76998"
            stroke-width="1.5"
            stroke-miterlimit="10"
            stroke-linecap="round"
            stroke-linejoin="round"
            id="path2" />
        <path
            d="m 2.5699846,5.26994 -1.57,-1.57998 1.57,-1.57"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
            id="path3" />
        </svg>
    </a>
    <a class="but align-left" href="new" with="50px" aling="left" title="Nuevo objetivo">
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

    <input type="text" id="searchInput" placeholder="Buscar dentro de objetivos..." class="search-input">

    <br><br>
    <table class="table-main">
        <tr>
            <th data-sortable="true">Objetivo</th>
            <th data-sortable="true">Descripción</th>
            <th></th>
        </tr>
        <?php if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                echo "<tr>";
                echo
                    "<td>
                        <a class=but  href='objetivo?id=" . $row["obj_id"] . "' title='Editar " . $row["obj_name"] . "'>" .
                        
                            $row["obj_name"] .
                            "</a>
                    </td>";
                echo "<td>" . $row["obj_desc"] . "</td>";
                ?>
                <td>
                    <a class="but align-left" href="objetivo/delete.php?id=<?php echo $row["obj_id"];?> " title="Borrar objetivo" onclick="return confirm('¿Estás seguro de que deseas borrar este objetivo?');">
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
            echo "<tr><td colspan='7'>No hay objetivos disponibles.</td></tr>";
        } ?>        
    </table>
</main>
<?php
include "../../.res/templates/footer.php";
?>