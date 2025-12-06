/**
 * auth.js - Autenticação com API Backend
 * Integração com api/auth.php
 */

const AuthAPI = (() => {
    const API_URL = './api/auth.php';

    /**
     * Fazer login
     */
    const login = async (cpf, senha) => {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    acao: 'login',
                    cpf: cpf,
                    senha: senha
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.erro || 'Erro no login');
            }

            return data;
        } catch (error) {
            console.error('Erro na autenticação:', error);
            throw error;
        }
    };

    /**
     * Registrar novo usuário
     */
    const registrar = async (nome, cpf, telefone, endereco, senha) => {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    acao: 'registrar',
                    nome: nome,
                    cpf: cpf,
                    telefone: telefone,
                    endereco: endereco,
                    senha: senha
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.erro || 'Erro no registro');
            }

            return data;
        } catch (error) {
            console.error('Erro no registro:', error);
            throw error;
        }
    };

    /**
     * Fazer logout
     */
    const logout = async () => {
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    acao: 'logout'
                })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Erro no logout:', error);
            throw error;
        }
    };

    return {
        login,
        registrar,
        logout
    };
})();