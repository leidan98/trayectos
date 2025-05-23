<?php
// debug_stored_procedure.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug del Stored Procedure</h2>";

try {
    require_once 'BD/baseDatos.php';
    require_once 'BD/session.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        die("❌ No se pudo conectar a la base de datos");
    }
    
    echo "✅ Conexión a BD exitosa<br>";
    
    // 1. Verificar si existe la tabla Tbl_Users
    echo "<h3>1. Verificando tabla Tbl_Users...</h3>";
    try {
        $query = "SHOW TABLES LIKE 'Tbl_Users'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabla 'Tbl_Users' existe<br>";
            
            // Mostrar estructura
            $query = "DESCRIBE Tbl_Users";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $columns = $stmt->fetchAll();
            
            echo "<strong>Estructura de Tbl_Users:</strong><br>";
            foreach ($columns as $col) {
                echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
            }
            
            // Contar registros
            $query = "SELECT COUNT(*) as total FROM Tbl_Users";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            echo "<br>Total de usuarios: " . $result['total'] . "<br>";
            
        } else {
            echo "❌ Tabla 'Tbl_Users' NO existe<br>";
            
            // Verificar tabla usuarios
            $query = "SHOW TABLES LIKE 'usuarios'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                echo "✅ Pero existe tabla 'usuarios'<br>";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Error verificando tablas: " . $e->getMessage() . "<br>";
    }
    
    // 2. Verificar si existe el stored procedure
    echo "<h3>2. Verificando Stored Procedure...</h3>";
    try {
        $query = "SHOW PROCEDURE STATUS WHERE Name = 'SP_AutenticarUsuario'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "✅ Stored Procedure 'SP_AutenticarUsuario' existe<br>";
            
            // Mostrar definición del SP
            $query = "SHOW CREATE PROCEDURE SP_AutenticarUsuario";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            
            echo "<strong>Definición del SP:</strong><br>";
            echo "<pre>" . htmlspecialchars($result['Create Procedure']) . "</pre>";
            
        } else {
            echo "❌ Stored Procedure 'SP_AutenticarUsuario' NO existe<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error verificando SP: " . $e->getMessage() . "<br>";
    }
    
    // 3. Test directo en la tabla
    echo "<h3>3. Test directo en la tabla...</h3>";
    ?>
    
    <form method="POST" style="border: 1px solid #ccc; padding: 20px; margin: 20px 0;">
        <h4>Probar consulta directa</h4>
        <div style="margin-bottom: 10px;">
            <label>Email:</label><br>
            <input type="email" name="test_email" required style="width: 300px; padding: 5px;">
        </div>
        <div style="margin-bottom: 10px;">
            <label>Password:</label><br>
            <input type="password" name="test_password" required style="width: 300px; padding: 5px;">
        </div>
        <button type="submit" name="test_direct" style="padding: 10px 20px;">Probar Consulta Directa</button>
    </form>
    
    <?php
    if (isset($_POST['test_direct'])) {
        $email = $_POST['test_email'];
        $password = $_POST['test_password'];
        $password_hash = hash('sha256', $password);
        
        echo "<h4>Resultado del test:</h4>";
        echo "Email: " . htmlspecialchars($email) . "<br>";
        echo "Password original: [OCULTO]<br>";
        echo "Password SHA256: " . $password_hash . "<br><br>";
        
        // Test 1: Buscar usuario por email
        echo "<strong>Test 1: Buscando usuario por email...</strong><br>";
        try {
            $query = "SELECT * FROM Tbl_Users WHERE email = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                echo "✅ Usuario encontrado:<br>";
                echo "- ID: " . $user['id_usuario'] . "<br>";
                echo "- Nombre: " . $user['nombre_usuario'] . "<br>";
                echo "- Email: " . $user['email'] . "<br>";
                echo "- Password en BD: " . $user['password'] . "<br>";
                echo "- Password calculado: " . $password_hash . "<br>";
                echo "- ¿Coinciden?: " . ($user['password'] === $password_hash ? 'SÍ' : 'NO') . "<br>";
            } else {
                echo "❌ Usuario no encontrado<br>";
            }
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "<br>";
        }
        
        // Test 2: Probar el SP si existe
        echo "<br><strong>Test 2: Probando Stored Procedure...</strong><br>";
        try {
            $stmt = $db->prepare("CALL SP_AutenticarUsuario(?, ?)");
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            $stmt->bindParam(2, $password_hash, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "✅ SP ejecutado exitosamente:<br>";
                echo "<pre>" . print_r($result, true) . "</pre>";
            } else {
                echo "❌ SP no retornó resultados<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ Error ejecutando SP: " . $e->getMessage() . "<br>";
        }
        
        // Test 3: Consulta directa con autenticación
        echo "<br><strong>Test 3: Consulta directa con autenticación...</strong><br>";
        try {
            $query = "SELECT id_usuario, nombre_usuario, email, tipo_usuario FROM Tbl_Users WHERE email = ? AND password = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$email, $password_hash]);
            
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "✅ Autenticación exitosa con consulta directa:<br>";
                echo "<pre>" . print_r($result, true) . "</pre>";
            } else {
                echo "❌ Autenticación falló con consulta directa<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ Error en consulta directa: " . $e->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "<br>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h2, h3 { color: #333; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    .success { color: green; }
    .error { color: red; }
</style>