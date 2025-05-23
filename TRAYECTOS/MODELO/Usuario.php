<?php 
class Usuario {
    private $conn;
    private $table_name = "Tbl_Users";
    public $id_usuario;
    public $nombre_usuario;
    public $email;
    public $password;
    public $tipo_usuario;
    public $activo;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function autenticar($email, $password) {
        try {
            // Verifica si la conexión existe
            if(!$this->conn) {
                throw new Exception("No hay conexión a la base de datos");
            }
            
            // Hashear la contraseña con SHA256
            $password_hash = hash('sha256', $password);
            
            // Log para debug (remover en producción)
            error_log("Intentando autenticar usuario: " . $email);
            error_log("Password hasheado: " . $password_hash);
            
            // Primero intentar con stored procedure
            try {
                $stmt = $this->conn->prepare("CALL SP_AutenticarUsuario(?, ?)");
                $stmt->bindParam(1, $email, PDO::PARAM_STR);
                $stmt->bindParam(2, $password_hash, PDO::PARAM_STR);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    error_log("Autenticación exitosa con SP para usuario: " . $email);
                    return $result;
                }
            } catch (PDOException $sp_error) {
                // Si el SP falla, usar consulta directa
                error_log("SP falló, usando consulta directa: " . $sp_error->getMessage());
                
                $stmt = $this->conn->prepare("SELECT id_usuario, nombre_usuario, email, tipo_usuario, activo FROM {$this->table_name} WHERE email = ? AND password = ? AND activo = 1");
                $stmt->execute([$email, $password_hash]);
                
                if ($stmt->rowCount() > 0) {
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    error_log("Autenticación exitosa con consulta directa para usuario: " . $email);
                    return $result;
                }
            }
            
            error_log("Autenticación falló para usuario: " . $email);
            return false;
            
        } catch (PDOException $e) {
            error_log("Error PDO en autenticación: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Error general en autenticación: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerTodos() {
        try {
            // Intentar con stored procedure primero
            try {
                $stmt = $this->conn->prepare("CALL SP_ObtenerUsuarios()");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $sp_error) {
                // Si el SP falla, usar consulta directa
                $stmt = $this->conn->prepare("SELECT id_usuario, nombre_usuario, email, tipo_usuario, activo FROM {$this->table_name} ORDER BY nombre_usuario");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log("Error obteniendo usuarios: " . $e->getMessage());
            return [];
        }
    }
    
    // Método helper para crear un usuario con contraseña hasheada
    public function crear($nombre_usuario, $email, $password, $tipo_usuario = 2) {
        try {
            $password_hash = hash('sha256', $password);
            
            $stmt = $this->conn->prepare("INSERT INTO {$this->table_name} (nombre_usuario, email, password, tipo_usuario, activo) VALUES (?, ?, ?, ?, 1)");
            return $stmt->execute([$nombre_usuario, $email, $password_hash, $tipo_usuario]);
            
        } catch (PDOException $e) {
            error_log("Error creando usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // Método para verificar si un email ya existe
    public function emailExiste($email) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table_name} WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>