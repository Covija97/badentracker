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
    <link rel="stylesheet" href="/badentracker/.res/css/styles.css">
    <link rel="icon" href="/badentracker/.res/icon/bt-logo.svg" type="image/svg+xml">
    
</head>

<body>
    <header>
        <a href="/" class="logo" title="Inicio">
            <img src="/badentracker/.res/icon/bt-logo.svg" height="50px">
            <h1>BadenTracker</h1>
        </a>
        <h1 class="titlePage">
            <?php if (isset($title)): ?>
                <?php echo $title; ?>
            <?php endif; ?>
        </h1>
        <div class="hright">
            <a href="/badentracker/actividades/" title="Actividades"
                <?php
                if ($page == "act")
                    echo 'class = "act"';
                else 
                    echo '';
                ?>
            >Actividades</a>
            <a href="/badentracker/reuniones/" title="Reuniones"
            <?php
                if ($page == "reu")
                    echo 'class = "act"';
                else 
                    echo '';
                ?>
            >Reuniones</a>
            <a href="/badentracker/calendario/" title="Calendario"
            <?php
                if ($page == "cald")
                    echo 'class = "act"';
                else 
                    echo '';
                ?>
            >Calendario</a>
        </div>
    </header>