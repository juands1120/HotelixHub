<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Camarero</title>
  <link rel="stylesheet" href="../assets/css/dashEmpleados.css" />
</head>
<body>

<div class="barra-lateral">
  <div class="logo">
    <a href="Home.php"><img src="../assets/img/imgHome/Logo Positivo.png" alt="HotelixHub" class="logo-img" /></a>
  </div>
  <br><br>

  <a href="dashEmpleado.php"><div class="menu-item">Inicio</div></a>




  <div class="espaciador"></div>

  <a href="../controller/logout.php"><div class="logout">Cerrar Sesión</div></a>
</div>

<div class="contenido">
  <h1>Bienvenido, <strong>Juan</strong></h1>

  <!-- Información Personal -->
  <section class="info-personal">
    <h2>Información Personal</h2>
    <div class="datos-personales">
      <div class="perfil-icono">👤</div>
      <div class="datos">
        <p><strong>Juan Mauricio Perez Florez</strong></p>
        <p>C.C. 32546223</p>
        <p>Nacionalidad: Colombia</p>
      </div>
      <div class="contacto">
        <p>Correo: juanma@gmail.com</p>
        <p>Rol: Camarero</p>
        <p>Cel: 3003265920</p>
        <p>Dirección: calle 56 #30-65</p>
      </div>
    </div>
  </section>

  <!-- Notificaciones -->
  <section class="notificaciones">
    <h2>Notificaciones</h2>
    <div class="cabecera-notificaciones">
      <span>Notificación</span>
      <span>Estado</span>
    </div>

    <!-- Mensajes cargados desde el backend -->
    <?php
    include '../config/conexionbd.php';

    try {
        $stmt = $pdo->query("CALL sp_listar_mensajes_empleado()");
        $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error al cargar mensajes: " . $e->getMessage() . "</p>";
    }
    ?>

    <?php if (!empty($mensajes)) : ?>
      <?php foreach ($mensajes as $m) : ?>
        <div class="card-mensaje">
          <p><strong><?= htmlspecialchars($m['cliente']) ?> - <?= htmlspecialchars($m['habitacion']) ?></strong></p>
          <p><?= nl2br(htmlspecialchars($m['mensaje'])) ?></p>
          <p><strong>Estado actual:</strong> <?= $m['estado'] ?> | <strong>Fecha:</strong> <?= $m['fecha_envio'] ?></p>

          <form action="../controller/actualizarEstado.php" method="POST" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;">
            <input type="hidden" name="id_mensaje" value="<?= $m['id_mensaje'] ?>">

            <?php if ($m['estado'] !== 'Pendiente') : ?>
              <button type="submit" name="estado" value="Pendiente" style="background-color:#ffc107; color:#000; border:none; padding:5px 10px; border-radius:4px;">
                🕓 Pendiente
              </button>
            <?php endif; ?>

            <?php if ($m['estado'] !== 'En preparación') : ?>
              <button type="submit" name="estado" value="En preparación" style="background-color:#17a2b8; color:#fff; border:none; padding:5px 10px; border-radius:4px;">
                🔄 En preparación
              </button>
            <?php endif; ?>

            <?php if ($m['estado'] !== 'Completado') : ?>
              <button type="submit" name="estado" value="Completado" style="background-color:#28a745; color:#fff; border:none; padding:5px 10px; border-radius:4px;">
                ✅ Completado
              </button>
            <?php endif; ?>
          </form>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <p>No hay mensajes por ahora.</p>
    <?php endif;

