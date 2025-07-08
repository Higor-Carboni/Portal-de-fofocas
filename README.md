# Portal de Fofocas - Sistema de Gerenciamento de Notícias

## 📋 Descrição
Sistema completo de gerenciamento de notícias com controle de usuários, sistema de aprovação e dashboard administrativo.

## 🚀 Funcionalidades

### CRUD de Notícias/Anúncios
- **Criar**: `cadastroNoticia.php` - Formulário completo para cadastro de notícias
- **Ler**: `noticia.php` - Visualização individual de notícias
- **Atualizar**: `alterarNoticia.php` - Edição de notícias existentes
- **Excluir**: `excluir_noticia.php` - Exclusão com confirmação

### Sistema de Usuários
- **Criar**: `cadastroUsuario.php` - Cadastro de novos usuários
- **Ler**: `usuarios.php` - Listagem de todos os usuários
- **Atualizar**: `editarUsuario.php` - Edição de usuários
- **Excluir**: Integrado em `usuarios.php`

### Sistema de Aprovação
- **Solicitações**: `solicitar.php` - Usuários solicitam edição/exclusão
- **Painel Admin**: `painelSolicitacoes.php` - Administradores aprovam/rejeitam
- **Processamento**: `processarSolicitacao.php` - Processa as solicitações

### Sistema de Anúncios (CRUD Completo)
- **Criar**: `cadastroAnuncio.php` - Cadastro de anúncios
- **Ler**: `anuncios.php` - Gerenciamento de anúncios
- **Atualizar**: `editarAnuncio.php` - Edição de anúncios
- **Excluir**: Integrado em `anuncios.php`
- **Exibição**: Seção de anúncios na página inicial
- **Pop-up**: Anúncios promocionais aleatórios

### Dashboard e Controle
- **Dashboard**: `dashboard.php` - Painel principal com estatísticas
- **Admin**: `admin.php` - Área administrativa
- **Autenticação**: `login.php`, `logout.php`, `verifica_login.php`

## 📁 Estrutura de Arquivos

### Arquivos Principais do CRUD
```
cadastroNoticia.php      # Criar notícias
alterarNoticia.php       # Editar notícias
excluir_noticia.php      # Excluir notícias
noticia.php              # Visualizar notícias
```

### Sistema de Usuários
```
cadastroUsuario.php      # Criar usuários
editarUsuario.php        # Editar usuários
usuarios.php             # Listar usuários
alterarUsuario.php       # Alterar perfil próprio
```

### Sistema de Aprovação
```
solicitar.php            # Solicitar edição/exclusão
painelSolicitacoes.php   # Painel de solicitações
processarSolicitacao.php # Processar solicitações
```

### Sistema de Anúncios
```
cadastroAnuncio.php      # Criar anúncios
editarAnuncio.php        # Editar anúncios
anuncios.php             # Gerenciar anúncios
```

### Arquivos de Suporte
```
conexao.php              # Conexão com banco de dados
funcoes.php              # Funções auxiliares
verifica_login.php       # Verificação de login
verificaAdmin.php        # Verificação de admin
```

## 🎨 Estilos CSS
```
css/
├── style.css            # Estilos principais
├── dashboard.css        # Estilos do dashboard
├── header.css           # Estilos do header
├── headerAdmin.css      # Estilos do header admin
├── footer.css           # Estilos do footer
├── usuarios.css         # Estilos da área de usuários
└── login.css            # Estilos do login
```

## 🔧 Configuração

### Banco de Dados
1. Importe o arquivo `dump.sql` no seu banco MySQL
2. Configure a conexão em `conexao.php`

### Permissões
- **Admin**: Acesso total ao sistema
- **Comum**: Pode criar notícias e solicitar edições/exclusões

### Upload de Imagens
- Formatos suportados: JPG, JPEG, PNG, GIF, WEBP
- Pasta de destino: `img/`
- Nomes únicos gerados automaticamente

## 🔒 Segurança
- Validação de sessões em todas as páginas
- Verificação de permissões por perfil
- Sanitização de dados de entrada
- Hash de senhas com `password_hash()`
- Proteção contra SQL Injection com prepared statements

## 📱 Responsividade
- Design responsivo para mobile e desktop
- Interface adaptativa baseada no perfil do usuário
- Componentes Bootstrap integrados

## 🎯 Fluxo de Trabalho

### Para Usuários Comuns:
1. Login no sistema
2. Acessar dashboard
3. Criar nova notícia (aguarda aprovação)
4. Solicitar edição/exclusão de notícias próprias

### Para Administradores:
1. Login como admin
2. Acessar dashboard com estatísticas
3. Gerenciar usuários
4. Aprovar/rejeitar solicitações
5. Editar/excluir qualquer notícia

## 🐛 Correções Implementadas
- ✅ Removidos arquivos duplicados (`editar_noticia.php`, `nova_noticia.php`, `editar_usuario.php`)
- ✅ Criado arquivo CSS faltante (`dashboard.css`)
- ✅ Corrigidos links e referências de arquivos
- ✅ Padronizada estrutura HTML em todos os arquivos
- ✅ Melhorado sistema de feedback com SweetAlert2
- ✅ Corrigido suporte a formato WEBP em uploads
- ✅ Removida opção de perfil "normal" inconsistente
- ✅ Melhorado tratamento de erros e validações

## 🆕 Novas Funcionalidades
- ✅ CRUD completo de anúncios para administradores
- ✅ Sistema de controle de status (ativo/inativo)
- ✅ Sistema de destaque para anúncios especiais
- ✅ Exibição de anúncios na página inicial
- ✅ Pop-up promocional com anúncios aleatórios
- ✅ Controle de valores dos anúncios
- ✅ Interface responsiva para anúncios

## 📞 Suporte
Para dúvidas ou problemas, verifique:
1. Configuração do banco de dados
2. Permissões de pasta para uploads
3. Configuração do PHP (extensões necessárias)
4. Logs de erro do servidor