<?php

$page = "reu";
$title = "Grupos";

require "../../.res/funct/funct.php";
include "../../.res/templates/header.php";

$sql = "SELECT grp_id, grp_name FROM grps ORDER BY grp_name ASC";
$query = linkDB()->query($sql);
?>
<style>
  .grupo-card {
    transition: box-shadow 0.2s;
    box-shadow: 0 0 30px -10px var(--color004);
    border: 2px solid var(--color004);
  }

  .grupo-card:hover {
    transform: scale(1.1);
    box-shadow: inset 0 0 10px var(--color005);
    border: 2px solid var(--color005);
  }

  .search-input {
    width: 300px;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 1px solid var(--color004);
    font-size: 1rem;
    margin-bottom: 1rem;
    outline: none;
    transition: border 0.2s;
  }

  .search-input:focus {
    border: 1.5px solid var(--color005);
    box-shadow: 0 0 6px var(--color00533);
  }

  .but.align-left {
    margin-right: 1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
  }

  .falta-logo {
    color: #b00;
    font-size: 0.95em;
    margin-bottom: 0.5rem;
    font-weight: bold;
    text-align: center;
  }
</style>
<main>
  <a class="but align-left" href="../" title="Volver a Grupos">
    <!-- SVG de volver -->
    <svg width="400" height="400" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path
        d="m 3.0000046,9.88004 h 4.91998 c 1.7,0 3.0800004,-1.38 3.0800004,-3.08 0,-1.7 -1.3800004,-3.07999 -3.0800004,-3.07999 h -6.76998"
        stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
      <path d="m 2.5699846,5.26994 -1.57,-1.57998 1.57,-1.57" stroke-width="1.5" stroke-linecap="round"
        stroke-linejoin="round" />
    </svg>
  </a>
  <a class="but align-left" href="new" title="Nuevo Grupo">
    <svg width="400" height="400" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
      <path d="m 2,6 h 8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
      <path d="M 6,10 V 2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
  </a>
  <input type="text" id="searchInput" placeholder="Buscar grupo..." class="search-input">
  <br><br>
  <div style="display: flex; flex-wrap: wrap; gap: 2rem; justify-content: flex-start;">
    <?php if ($query->num_rows > 0) {
      $bg = 'var(--color001)';
      $fg = 'var(--color002)';
      while ($row = $query->fetch_assoc()) {
        $logoPath = "../../.res/img/logos-grupos/" . $row["grp_id"] . ".png";
        echo "<div class='grupo-card' style='width:180px; background:$bg; border-radius:12px; display:flex; flex-direction:column; align-items:center; padding:1.2rem 1rem 1.5rem 1rem; position:relative;'>";
        echo "<a href='grupo?id=" . $row["grp_id"] . "' title='Grupo Scout " . $row["grp_name"] . "' style='display:block; width:100%; text-align:center; color:$fg; text-decoration:none;'>";
        echo "<img src='" . $logoPath . "' alt='Logo' style='width:100%; border-radius:8px; display:block; margin:0 auto;' onerror=\"this.style.display='none'; this.nextElementSibling.style.display='block';\">";
        echo "<div class='falta-logo' style='display:none; color:#b00; font-size:0.95em; margin-bottom:0.5rem;'>falta logo</div>";
        echo "<div style='font-weight:bold; color:$fg; text-align:center;'>" . htmlspecialchars($row["grp_name"]) . "</div>";
        echo "</a>";
        echo "</div>";
      }
    } else {
      echo "<div style='color:#888;'>No hay grupos disponibles.</div>";
    } ?>
  </div>
  <script>
    // Filtrado de grupos por búsqueda
    document.getElementById('searchInput').addEventListener('input', function () {
      const filtro = this.value.toLowerCase();
      document.querySelectorAll('.grupo-card').forEach(function (card) {
        const nombre = card.querySelector('div[style*="font-weight:bold"]');
        if (nombre && nombre.textContent.toLowerCase().includes(filtro)) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  </script>
</main>
<?php
include "../../.res/templates/footer.php";
?>