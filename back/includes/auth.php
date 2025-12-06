<?php
/**
 * Funções de Autenticação
 */

/**
 * Verificar se usuário está autenticado
 */
function estaAutenticado() {
    return isset($_SESSION['usuario_id']);
}

/**
 * Obter usuário autenticado
 */
function obterUsuarioAutenticado($db) {
    if (!estaAutenticado()) return null;

    $sql = "SELECT id, nome, cpf, telefone, endereco FROM usuarios WHERE id = ?";
    $result = $db->query($sql, [$_SESSION['usuario_id']]);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Fazer login
 */
function fazerLogin($db, $cpf, $senha) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    $sql = "SELECT id, nome, senha FROM usuarios WHERE cpf = ?";
    $result = $db->query($sql, [$cpf]);
    
    if ($result && $result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
        if (verificarSenha($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            return true;
        }
    }
    
    return false;
}

/**
 * Fazer logout
 */
function fazerLogout() {
    session_destroy();
    return true;
}

/**
 * Registrar novo usuário
 */
function registrarUsuario($db, $nome, $cpf, $telefone, $endereco, $senha) {
    // Validações
    if (strlen($nome) &lt; 3) {
        return ['erro' => 'Nome deve ter pelo menos 3 caracteres'];
    }

    if (!validarCPF($cpf)) {
        return ['erro' => 'CPF inválido'];
    }

    if (strlen($senha) &lt; 6) {
        return ['erro' => 'Senha deve ter pelo menos 6 caracteres'];
    }

    // Verificar se CPF já existe
    $sql = "SELECT id FROM usuarios WHERE cpf = ?";
    $cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);
    $result = $db->query($sql, [$cpf_limpo]);

    if ($result && $result->num_rows > 0) {
        return ['erro' => 'CPF já cadastrado'];
    }

    // Inserir usuário
    $senhaHash = hashSenha($senha);
    $sql = "INSERT INTO usuarios (nome, cpf, telefone, endereco, senha) VALUES (?, ?, ?, ?, ?)";
    $result = $db->execute($sql, [$nome, $cpf_limpo, $telefone, $endereco, $senhaHash]);

    if ($result) {
        return ['sucesso' => true, 'mensagem' => 'Usuário registrado com sucesso'];
    }

    return ['erro' => 'Erro ao registrar usuário'];
}