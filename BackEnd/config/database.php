<?php
/**
 * Configuração de Conexão MySQL
 */

// Credenciais do banco
define('DB_HOST', 'localhost');
define('DB_USER', 'winnerte_winnertecnolog05');
define('DB_PASS', '781226Edu');
define('DB_NAME', 'winnerte_winnertecnolog05');
define('DB_CHARSET', 'utf8mb4');

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Classe de Conexão com MySQL
 */
class Database {
    private $connection;

    public function __construct() {
        try {
            $this->connection = new mysqli(
                DB_HOST,
                DB_USER,
                DB_PASS,
                DB_NAME
            );

            // Verificar conexão
            if ($this->connection->connect_error) {
                throw new Exception('Erro na conexão: ' . $this->connection->connect_error);
            }

            // Definir charset
            $this->connection->set_charset(DB_CHARSET);
        } catch (Exception $e) {
            die(json_encode(['erro' => 'Erro de conexão com banco de dados']));
        }
    }

    /**
     * Executar query SELECT
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            
            if (!$stmt) {
                throw new Exception('Erro na preparação: ' . $this->connection->error);
            }

            // Bind de parâmetros
            if (!empty($params)) {
                $types = '';
                foreach ($params as $param) {
                    if (is_int($param)) $types .= 'i';
                    elseif (is_float($param)) $types .= 'd';
                    else $types .= 's';
                }
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            return $stmt->get_result();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Executar INSERT, UPDATE, DELETE
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            
            if (!$stmt) {
                throw new Exception('Erro na preparação: ' . $this->connection->error);
            }

            if (!empty($params)) {
                $types = '';
                foreach ($params as $param) {
                    if (is_int($param)) $types .= 'i';
                    elseif (is_float($param)) $types .= 'd';
                    else $types .= 's';
                }
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            return $stmt->affected_rows;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obter último ID inserido
     */
    public function lastInsertId() {
        return $this->connection->insert_id;
    }

    /**
     * Fechar conexão
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

// Criar instância global
$db = new Database();