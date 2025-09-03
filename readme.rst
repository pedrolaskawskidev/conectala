# Projeto API Conecta Lá

# Projeto

Fazer uma API com um CRUD de usuários, visando segurança, tratamento de dados de entrada e boas práticas.
Foi feito com Code Igniter 3 com PHP 8.1 (brew-php).


# Passos


* Fazer PULL do projeto
* Iniciar o servidor do PHP (NGNIX/APACHE) _eu usei o PHP Server do VSCode_
* Configurar o acesso ao banco de dados e criar-lo
* Acessar a rota 
    * GET /db/migrate - para criar a tabela 
    * GET /db/seed - para criar o usuário 
        *username: teste
        *password: teste01
* Logar no sistema e fazer os testes nas rotas:
    * GET /api
        * Mostra todos os usuários
    * GET /api/show/id
        * Mostra o usuário do ID fornecido
    * POST /api/create
        * Body (em JSON)
            * "username": "string", "email": "email/string", "password": "string"
    * PUT /api/update/id
        * Body (em JSON)
            * "username": "string", 'sem email', 'sem password' - Pode enviar parcialmente o body
    * DELETE /api/delete/id
        * Deleta o id do usuário
* Rotas de login/logout:
    * POST /auth/login
        * Form Data
            * username / password
    * POST /auth/logout
        * exclui o token e encerra a sessão
