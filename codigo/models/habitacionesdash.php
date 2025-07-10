<?php
function obtenerEstadisticasPiso($piso, $pdo) {
    $stmt = $pdo->prepare("CALL sp_estadisticas_habitaciones(:piso)");
    $stmt->bindParam(':piso', $piso, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); // importante si haces múltiples CALL
    return $result;
}
?>
