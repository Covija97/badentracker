<?php
$page = "";

include ".res/templates/header.php";

?>
<main>
<p style="text-align: left; font-size: 1em;   padding: 20px 20px 0px 20px;">
    <b>BadenTracker</b> es una aplicación para ayudar a los grupos scouts a planificar y gestionar sus reuniones de forma eficiente. 
    <ul style="text-align: left; font-size: 1em;">
        <li>Permite crear y gestionar <b>actividades</b> de forma sencilla.</li>
        <li>Ofrece un <b>calendario</b> interactivo para visualizar y organizar reuniones.</li>
        <li>Incluye una <b>base de datos de actividades</b> filtrables por objetivos pedagógicos y tipo de actividad.</li>
        <li>Facilita la <b>exportación de programaciones en PDF</b> con un formato específico.</li>
    </ul>
</p>

<div style="display: flex; justify-content: center; gap: 20px; padding: 20px;">
    <img src=".res/img/bt-01-es.png">
    <img src=".res/img/bt-11-es.png">
</div>

</main>
<?php
include ".res/templates/footer.php";
?>