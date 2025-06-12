# Portal de Notícias

Este é um projeto de portal de notícias desenvolvido em PHP com MySQL, utilizando PDO para acesso ao banco de dados. O sistema permite cadastro, autenticação de usuários, publicação, edição e exclusão de notícias, além de gerenciamento de categorias e usuários (admin).

## Funcionalidades

- Cadastro e login de usuários
- Perfis de usuário (admin e comum)
- CRUD de notícias com upload de imagem
- Filtro de notícias por categoria
- Dashboard com estatísticas e gráficos (Chart.js)
- Gerenciamento de usuários (apenas para admin)
- Sistema de permissões (admin e usuário comum)
- Segurança com hash de senha (password_hash)
- Interface responsiva e moderna

## Instalação

1. **Clone o repositório ou copie os arquivos para seu servidor local (ex: XAMPP).**

2. **Crie o banco de dados:**
   - Importe o arquivo `dump.sql` no seu MySQL.

3. **Configure a conexão:**
   - Edite o arquivo `conexao.php` se necessário, ajustando usuário, senha e nome do banco.

4. **Acesse pelo navegador:**
   - Exemplo: `http://localhost/porta-noticias1/`

## Estrutura de Pastas

- `index.php` — Página inicial com listagem e filtro de notícias
- `login.php` — Tela de login
- `cadastroUsuario.php` — Cadastro de novos usuários
- `dashboard.php` — Painel do usuário logado
- `cadastroNoticia.php` — Cadastro de notícias
- `alterarNoticia.php` — Edição de notícias
- `usuarios.php` — Gerenciamento de usuários (admin)
- `verifica_login.php` — Proteção de páginas restritas
- `verificaAdmin.php` — Proteção de páginas de admin
- `uploads/` — Pasta para imagens das notícias

## Usuário Admin

Para criar um usuário admin, adicione manualmente o campo `perfil` com valor `'admin'` na tabela `usuarios` pelo phpMyAdmin ou via SQL:

```sql
ALTER TABLE usuarios ADD COLUMN perfil VARCHAR(20) DEFAULT 'usuario';
UPDATE usuarios SET perfil = 'admin' WHERE email = 'seu@email.com';