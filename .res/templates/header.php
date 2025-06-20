<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>
        BadenTracker
        <?php if (isset($title)): ?>
            - <?php echo $title; ?>
        <?php endif; ?>
    </title>
    <link rel="stylesheet" href="/.res/css/styles.css?v=2">
    <link rel="icon" href="/.res/icon/bt-icon.svg">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body>
    <header>
        <a href="/" class="logo" title="Inicio">
            <img src="/.res/icon/bt-logo.svg" height="50px">
            <h1>BadenTracker</h1>
        </a>
        <h1 class="titlePage">
            <?php if (isset($title)): ?>
                <?php echo $title; ?>
            <?php endif; ?>
        </h1>

        <div class="hright">
            <?php if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin'): ?>
                <a href="/.admin" title="Panel de administración">
                    Admin
                </a>
            <?php endif; ?>
            <a href="/actividades/" title="Ir a actividades" <?php
            if ($page == "act")
                echo 'class = "act"';
            else
                echo '';
            ?>>Actividades</a>
            <a href="/reuniones/" title="Ir a reuniones" <?php
            if ($page == "reu")
                echo 'class = "act"';
            else
                echo '';
            ?>>Reuniones</a>
            <a href="/calendario/" title="Ir a calendario" <?php
            if ($page == "cald")
                echo 'class = "act"';
            else
                echo '';
            ?>>Calendario</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/logout.php" title="Cerrar sesión" class="but align-right">
                    <svg width="400" height="400" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M 4.3746182,3.6667944 C 4.537156,1.7792594 5.5071288,1.0085159 7.6306057,1.0085159 h 0.068161 c 2.3436893,0 3.2822143,0.9385244 3.2822143,3.2822138 v 3.4185409 c 0,2.3436894 -0.938525,3.2822134 -3.2822143,3.2822134 h -0.068161 c -2.1077475,0 -3.0777203,-0.760257 -3.2507443,-2.616333"
                            stroke-width="0.832521" stroke-linecap="round" stroke-linejoin="round" id="path1"
                            style="stroke-width:1.41703;stroke-dasharray:none" />
                        <path d="M 7.572931,5.9947596 H 1.6062229" stroke-width="0.832521" stroke-linecap="round"
                            stroke-linejoin="round" id="path2" style="stroke-width:1.41703;stroke-dasharray:none" />
                        <path d="M 2.7754461,4.2382509 1.0189898,5.9947071 2.7754461,7.7511635" stroke-width="0.832521"
                            stroke-linecap="round" stroke-linejoin="round" id="path3"
                            style="stroke-width:1.41703;stroke-dasharray:none" />
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </header>