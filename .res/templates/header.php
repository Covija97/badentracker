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
    <link rel="stylesheet" href="/badentracker/.res/css/styles.css?v=2">
    <link rel="icon" href="/badentracker/.res/icon/bt-icon.svg">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
</head>

<body>
    <header>
        <a href="/badentracker" class="logo" title="Inicio">
            <img src="/badentracker/.res/icon/bt-logo.svg" height="50px">
            <h1>BadenTracker</h1>
        </a>
        <h1 class="titlePage">
            <?php if (isset($title)): ?>
                <?php echo $title; ?>
            <?php endif; ?>
        </h1>
        <div class="hright">
            <a href="/badentracker/actividades/" title="Ir a actividades"
                <?php
                if ($page == "act")
                    echo 'class = "act"';
                else 
                    echo '';
                ?>
            >Actividades</a>
            <a href="/badentracker/reuniones/" title="Ir a reuniones"
            <?php
                if ($page == "reu")
                    echo 'class = "act"';
                else 
                    echo '';
                ?>
            >Reuniones</a>
            <a href="/badentracker/calendario/" title="Ir a calendario"
            <?php
                if ($page == "cald")
                    echo 'class = "act"';
                else 
                    echo '';
                ?>
            >Calendario</a>
        </div>
    </header>