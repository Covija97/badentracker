<?php
session_start();
$title = "Calendario";
$page = "cald";

include "../.res/templates/header.php";
require "../.res/funct/funct.php"; // Usa la función linkDB() como en actividades

$conn = linkDB();
$events = [];
// Consulta para obtener las programaciones de reuniones
$sql = "SELECT prog_id, prog_date AS start, rama.rama_id, CONCAT(rama_name, ' - ', grp_name) AS title
        FROM prog
        JOIN grps ON prog.grp_id = grps.grp_id
        JOIN rama ON prog.rama_id = rama.rama_id";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Asigna color según rama_id
        switch ($row['rama_id']) {
            case 2:
                $row['color'] = '#fbc04d';
                $row['textColor'] = '#000';
                break; // Naranja Castores, texto negro
            case 3:
                $row['color'] = '#feda2a';
                $row['textColor'] = '#000';
                break; // Amarillo Lobatos, texto negro
            case 4:
                $row['color'] = '#06abd8';
                $row['textColor'] = '#fff';
                break; // Azul Rangers, texto blanco
            case 5:
                $row['color'] = '#ec2726';
                $row['textColor'] = '#fff';
                break; // Rojo Pioneros, texto blanco
            case 6:
                $row['color'] = '#009d4a';
                $row['textColor'] = '#fff';
                break; // Verde Rutas, texto blanco
            default:
                $row['color'] = '#757575';
                $row['textColor'] = '#fff'; // Gris por defecto, texto blanco
        }
        $row['url'] = '../reuniones/reunion?id=' . $row['prog_id'];
        $events[] = $row;
    }
}
// Obtener los grupos para el filtro
$grupos = [];
$grpsql = "SELECT grp_id, grp_name FROM grps ORDER BY grp_name";
$grpres = $conn->query($grpsql);
if ($grpres) {
    while ($g = $grpres->fetch_assoc()) {
        $grupos[] = $g;
    }
}
?>
<!---------------------------------------- CSS ---------------------------------------->
<link rel="stylesheet" href="../../.res/css/reunion.css">
<!---------------------------------------- main ---------------------------------------->
<main>
    <a class="but align-left" href="../reuniones/new" align="left" title="Nueva Reunión" aria-label="Nueva Reunión">
        <svg width="400" height="400" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
            <path d="m 2,6 h 8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" id="path1" />
            <path d="M 6,10 V 2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" id="path2" />
        </svg>
    </a>
    <br><br>
    <div id="calendar"></div>
    <!-- El select se mueve fuera del flujo normal y se posiciona sobre el header del calendario -->
    <div id="grupoFiltroContainer">
        <label for="grupoFiltro" style="margin-right:10px;">Grupo:</label>
        <select id="grupoFiltro" class="select2 but" style="width: 200px;">
            <option value="">Todos</option>
            <?php foreach ($grupos as $g): ?>
                <option value="<?php echo $g['grp_name']; ?>"><?php echo $g['grp_name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</main>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="../.res/js/scripts.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#grupoFiltro').select2();

        var eventos = <?php echo json_encode($events); ?>;
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: '' // Sin botón de mes
            },
            buttonText: {
                today: 'Hoy'
            },
            events: eventos
        });
        calendar.render();

        // Reposicionar el filtro justo al lado derecho del header del calendario
        function posicionarFiltro() {
            var header = document.querySelector('.fc-header-toolbar .fc-toolbar-chunk:last-child');
            var filtro = document.getElementById('grupoFiltroContainer');
            if (header && filtro) {
                header.appendChild(filtro);
                filtro.style.position = 'static';
                filtro.style.marginLeft = '20px';
                filtro.style.top = '';
                filtro.style.right = '';
            }
        }
        setTimeout(posicionarFiltro, 300);
        // Por si FullCalendar vuelve a renderizar el header
        calendar.on('datesSet', posicionarFiltro);

        $('#grupoFiltro').on('change', function () {
            var grupo = $(this).val();
            var filtrados = grupo
                ? eventos.filter(e => removeAccents(e.title.toLowerCase()).includes(removeAccents(grupo.toLowerCase())))
                : eventos;
            calendar.removeAllEvents();
            calendar.addEventSource(filtrados);
        });
    });
</script>
<style>
    .fc-toolbar-title {
        text-transform: lowercase;
    }
    .fc-toolbar-title::first-letter {
        text-transform: uppercase;
    }
</style>
<?php
include "../.res/templates/footer.php";
?>