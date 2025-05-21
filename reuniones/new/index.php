<!-- Variables, require e include -->
<?php
$title = "Nueva Reunión";
$page = "reu";

require "../../.res/funct/funct.php";
include "../../.res/templates/header.php";
?>
<!-- Agregar en el <head> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Consultas -->

<!-------------------- Consultas generales -------------------->

<?php
/* Consulta todas las actividades */
$allActSQL = "
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
$allActQuery = linkDB()->query($allActSQL);
$allActs = $allActQuery->fetch_all(MYSQLI_ASSOC);
?>

<?php
/* Consulta todas las ramas */
$allRamSQL = "SELECT rama_id, rama_name FROM rama";
$allRamQuery = linkDB()->query($allRamSQL);
$allRams = $allRamQuery->fetch_all(MYSQLI_ASSOC);
?>

<?php
/* Consulta todos los grupos */
$allGrpSQL = "SELECT grp_id, grp_name FROM grps";
$allGrpQuery = linkDB()->query($allGrpSQL);
$allGrps = $allGrpQuery->fetch_all(MYSQLI_ASSOC);
?>

<!-------------------- POST -------------------->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

}
?>

<style>
    :root {
        --colorRama : #efefef;
    }
    .branchCell {
        background-color: var(--colorRama);
    }
    table {
        font-family: "Calibri", sans-serif;
        overflow: hidden;
        width: 100%;
        font-size: 20px;
        line-height: 1.5;
        text-align: left;
        vertical-align: middle;  
        border: 2px solid var(--color004);
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 10px;
    }

    td {
        background-color: white;
        text-align: center;
        height: 30px;
        border: 0.5px solid #bebebe;
        padding: 10px;
    }
    
    .select2-container {
        width: 100% !important;
        font-family: "Calibri", sans-serif;
        text-align: center;
    }

    
    input {
        width: 100%;
        height: 100%;
        padding: 0px;
        border: 0.5px solid var(--color004);
        border-radius: 0px ;
        background-color: #ffffffcc;
        color: var(--color002);
        font-size: 18px;
        text-align: center;
    }
    .logoCell {
        width: 150px;
        height: 150px;
        padding: 0;
        text-align: center;
    }
    .logoCell img {
        width: 100%;
        height: 100%;
        object-fit: contain; /* Esto evitará que la imagen se deforme */
    }

</style>

<!-------------------- main -------------------->

<main>
    <a class="but align-left" href="../index.php" title="Volver a Reuniones">
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
    <br><br>

    <form method="POST" class="form-grid">
        <input type="hidden" name="act_id">
        <!-- Tabla de información general -->
        <table>
            <tr>
                <td class="branchCell" width="25%">
                    <label for="grop_id">
                        <b>Grupo scout</b>
                    </label>
                </td>
                <td width="35%">
                    <select name="grps[]" id="grps" class="select2" onchange="cambioLogo()">
                        <option value="" selected disabled>Selecciona el grupo</option>
                        <?php
                        foreach ($allGrps as $grp) {
                            echo "<option value='" . $grp["grp_id"] . "'>" . $grp["grp_name"] . "</option>";
                        }
                        ?>
                    </select>
                </td>
                <td class="branchCell" width="20%">
                    Ronda Solar
                </td>
                <td width="20%">
                    <span id="rondaSolar">
                        Calculo automático...
                    </span>
                </td>
            </tr>
            <tr>
                <td class="branchCell">
                    <label for="prog_place">Lugar</label>
                </td>
                <td>
                    <input type="text" name="" id="">
                </td>
                <td class="branchCell">
                    <label for="prog_date">Fecha</label>
                </td>
                <td>
                    <input type="date" name="prog_date" id="prog_date" onchange="calculaRondaSolar()">
                </td>
            </tr>
            <tr>
                <td class="branchCell">
                    <label for="prog_coord">Coordinador</label>
                </td>
                <td>
                    <input type="text" name="prog_coord" id="prog_coord">
                </td>
                <td class="branchCell">
                    <label for="prog_child_N">Nº educandos</label>
                </td>
                <td>
                    <input type="number" name="prog_child_N" id="prog_child_N">
                </td>
            </tr>
            <tr>
                <td class="branchCell">
                    <label for="rama">
                        Rama
                    </label>
                </td>
                <td class="branchCell" colspan="2">
                    <label for="prog_child_N">
                        Responsables asistentes
                    </label>
                </td>
                <td class="branchCell">
                    Logo del grupo
                </td>
            </tr>
            <tr>
                <td class="branchCell">
                    <select name="rama[]" id="rama" class="select2" onchange="colorearCeldas()">
                        <option value="" selected disabled>Selecciona la rama</option>
                        <?php
                        foreach ($allRams as $rama) {
                            echo "<option value='" . $rama["rama_id"] . "'>" . $rama["rama_name"] . "</option>";
                        }
                        ?>
                    </select>
                </td>
                <td colspan="2">

                </td>
                <td class="logoCell">
                    <img id="logoGrupo" src="" alt="Seleccione un grupo">
                </td>
            </tr>
        </table>

        <!-- Boton de añadir actividad -->
        <a class="but align-left" style="width: 40px;" title="Nueva Reunión" onclick="addActivity()">
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

        <!-- Tabla de actividades -->
        <div id="actContainer">

        </div>
    </form>
</main>

<!-------------------- Scripts de select2 -------------------->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

    $(document).ready(function () {
        $('.select2').select2();

        // Delegar evento para todos los selects con name="act[]"
        $(document).on('change', 'select[name="act[]"]', function () {
            const selected = $(this).find('option:selected');
            const contenedor = $(this).closest('.actividad');
            contenedor.find('.campo-categorias').text(selected.data('categorias') || '');
            contenedor.find('.campo-objetivos').html(selected.data('objetivos') || '');
            contenedor.find('.campo-materiales').html(selected.data('materiales') || '');
            contenedor.find('.campo-descripcion').text(selected.data('descripcion') || '');
        });
    });
    
    function calculaRondaSolar() {
        const fecha = document.getElementById('prog_date').value;
        let ronda = '';
        if (fecha) {
            // La ronda solar empieza en septiembre y termina en agosto
            const mes = new Date(fecha).getMonth() + 1; // Los meses van de 0 a 11
            const anio = new Date(fecha).getFullYear(); // Obtener el año actual
            if (mes >= 9) {
                ronda = anio + '/' + (anio + 1) % 100;
            } else {
                ronda = (anio - 1) + '/' + anio % 100;
            }
        }
        document.getElementById('rondaSolar').textContent = ronda;
    }
    // Inicializar si ya hay valor
    document.getElementById('prog_date').addEventListener('input', calculaRondaSolar);

    function colorearCeldas() {
        const rama = document.getElementById('rama').value;
        console.log("Valor de rama:", rama);
        let color = '';
        switch (rama) {
            case '2':
                color = '#fce5cd';
                break;
            case '3':
                color = '#fff1cc';
                break;
            case '4':
                color = '#c9daf8';
                break;
            case '5':
                color = '#f4cccc';
                break;
            case '6':
                color = '#d9ead3';
                break;
            default:
                color = '#efefef';
        }
        // Actualizar la variable CSS
        document.documentElement.style.setProperty('--colorRama', color);
    }

    function cambioLogo() {
        const grupo = document.getElementById("grps").value;
        console.log("Grupo seleccionado:", grupo); // Para depuración

        // Construye la ruta de la imagen
        const rutaImagen = `../../.res/img/logos-grupos/${grupo}.png`;

        // Cambia la imagen
        document.getElementById("logoGrupo").src = rutaImagen;
    }

    // funcion para generar tablas usando el boton +
    let nActividades = 0;
    function addActivity(){
        const container = document.getElementById('actContainer');
        const index = nActividades++;

        const actividadHTML = `
            <div class="actividad" id="actividad-${index}">
                <table>
                    <tr>
                        <td class='branchCell' colspan='5'>
                            <button onclick="removeActivity(${index})" class="but align-right">
                                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 5.97998C17.67 5.64998 14.32 5.47998 10.98 5.47998C9 5.47998 7.02 5.57998 5.04 5.77998L3 5.97998"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.5 4.97L8.72 3.66C8.88 2.71 9 2 10.69 2H13.31C15 2 15.13 2.75 15.28 3.67L15.5 4.97"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.85 9.14001L18.2 19.21C18.09 20.78 18 22 15.21 22H8.79002C6.00002 22 5.91002 20.78 5.80002 19.21L5.15002 9.14001"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10.33 16.5H13.66"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9.5 12.5H14.5"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class='branchCell' width='25%'>Nombre</td>
                        <td width='35%'>
                            <select name="act[]"class="select2"">
                                <option value="" selected disabled>Selecciona una actividad</option>
                                <?php
                                foreach ($allActs as $act) {
                                    echo "
                                        <option value='" . $act["act_id"] . "' 
                                            data-objetivos=\"" . htmlspecialchars($act["act_objs"]) . "\" 
                                            data-materiales=\"" . htmlspecialchars($act["act_mats"]) . "\" 
                                            data-categorias=\"" . htmlspecialchars($act["act_cats"]) . "\" 
                                            data-descripcion=\"" . htmlspecialchars($act["act_desc"]) . "\"
                                            data-duracion=\"" . htmlspecialchars($act["act_durat"]) . "\">
                                            " . $act["act_name"] . "
                                        </option>
                                    ";                               
                                }
                                ?>
                            </select>
                        </td>
                        <td class='branchCell' width='20%'>Tipo</td>
                        <td width='10%'>
                            <span class='campo-categorias'></span>
                        </td>
                        <td width='10%'>
                            ${
                                index === 0
                                ? `<input type='time' name='actividades[${index}][hora]' class='hora-inicio'>`
                                : `<span class='hora-act'></span>`
                            }
                        </td>
                    </tr>
                    <tr>
                        <td class='branchCell'>Objetivos</td>
                        <td>
                            <span class='campo-objetivos'></span>
                        </td>
                        <td class='branchCell'>Materiales</td>
                        <td colspan='2'>
                            <span class='campo-materiales'></span>
                        </td>
                    </tr>
                    <tr>
                        <td class='branchCell' colspan='5'>Desarrollo</td>
                    </tr>
                    <tr>
                        <td colspan='5'>
                            <span class='campo-descripcion'></span>
                        </td>
                    </tr>
                </table>
                <br>
            </div>
            `;

        container.insertAdjacentHTML('beforeend', actividadHTML);
        $('#actividad-' + index + ' .select2').select2();

    } 


    function removeActivity(index) {
        const actividad = document.getElementById(`actividad-${index}`);
        if (actividad) {
            actividad.remove();
        }
    }

</script>
