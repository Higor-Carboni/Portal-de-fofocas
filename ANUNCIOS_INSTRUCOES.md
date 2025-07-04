# 📢 Sistema de Anúncios - Instruções

## 🎯 Funcionalidades Implementadas

### ✅ CRUD Completo de Anúncios
- **Criar**: `cadastroAnuncio.php` - Formulário para cadastrar novos anúncios
- **Ler**: `anuncios.php` - Listagem e gerenciamento de todos os anúncios
- **Atualizar**: `editarAnuncio.php` - Edição de anúncios existentes
- **Excluir**: Integrado em `anuncios.php` com confirmação

### ✅ Controle de Status
- **Ativo/Inativo**: Toggle para ativar ou desativar anúncios
- **Destaque**: Toggle para marcar anúncios em destaque
- **Valor**: Campo para registrar o valor cobrado pelo anúncio

### ✅ Exibição na Página Inicial
- **Seção de Anúncios**: `<div id="anuncios">` na `index.php`
- **Apenas Ativos**: Exibe apenas anúncios com `ativo = true`
- **Efeito Destaque**: Anúncios com `destaque = true` têm animação especial
- **Valores Visíveis**: Mostra o valor dos anúncios na listagem

### ✅ Anúncios Promocionais (Pop-up)
- **Seleção Aleatória**: Escolhe um anúncio ativo aleatoriamente
- **Modal Automático**: Aparece após 3 segundos do carregamento
- **Fechamento**: Pode ser fechado clicando no X ou fora do modal
- **Link Direto**: Botão "Ver Mais" leva para a URL do anunciante

## 🗄️ Estrutura da Tabela

```sql
CREATE TABLE anuncios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL COMMENT 'Nome da empresa ou anunciante',
    imagem VARCHAR(500) NOT NULL COMMENT 'Caminho da imagem/banner',
    link VARCHAR(500) NOT NULL COMMENT 'URL de destino (ex: site, promoção)',
    texto VARCHAR(255) NOT NULL COMMENT 'Mensagem ou slogan',
    ativo BOOLEAN DEFAULT TRUE COMMENT 'Controla se o anúncio deve ou não aparecer',
    destaque BOOLEAN DEFAULT FALSE COMMENT 'Se verdadeiro, o anúncio aparece com destaque',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de inclusão no sistema',
    valorAnuncio DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Valor cobrado pelo anuncio'
);
```

## 🔐 Controle de Acesso
- **Apenas Administradores**: Todo o CRUD de anúncios é restrito a usuários com perfil `admin`
- **Verificação**: Todos os arquivos usam `verificaAdmin.php`
- **Menu**: Link "Anúncios" aparece apenas no menu de administradores

## 🎨 Estilos e Animações
- **Anúncios Normais**: Design limpo e responsivo
- **Anúncios em Destaque**: Borda dourada e animação de pulso
- **Modal Promocional**: Aparece com animação suave
- **Hover Effects**: Efeitos visuais ao passar o mouse

## 📱 Responsividade
- **Mobile**: Anúncios se adaptam a telas pequenas
- **Desktop**: Layout otimizado para telas grandes
- **Imagens**: Redimensionamento automático mantendo proporções

## 🚀 Como Usar

### Para Administradores:
1. Acesse o menu "Administração" → "Anúncios"
2. Use o botão "+" para cadastrar novo anúncio
3. Gerencie status (ativo/inativo) e destaque
4. Edite ou exclua anúncios conforme necessário

### Para Visitantes:
1. Anúncios aparecem automaticamente na página inicial
2. Anúncios em destaque têm efeito visual especial
3. Pop-up promocional aparece após 3 segundos
4. Clique nos anúncios para visitar os sites dos anunciantes

## 📋 Campos Obrigatórios
- **Nome**: Nome da empresa ou anunciante
- **Imagem**: Banner/imagem do anúncio (JPG, PNG, GIF, WEBP)
- **Link**: URL de destino do anúncio
- **Texto**: Slogan ou mensagem do anúncio

## 📋 Campos Opcionais
- **Valor**: Valor cobrado pelo anúncio (para controle interno)
- **Ativo**: Checkbox para ativar/desativar
- **Destaque**: Checkbox para marcar como destaque

## 🎯 Exemplos de Uso
- **Restaurantes**: Promoções e cardápios
- **Lojas**: Produtos em destaque
- **Serviços**: Ofertas especiais
- **Eventos**: Divulgação de eventos

## 🔧 Configuração
1. Importe o arquivo `dump.sql` atualizado
2. Certifique-se de que a pasta `img/` tem permissões de escrita
3. Acesse como administrador para gerenciar anúncios
4. Teste a exibição na página inicial

## 📞 Suporte
Para dúvidas ou problemas:
1. Verifique se o usuário tem perfil `admin`
2. Confirme se as imagens estão na pasta `img/`
3. Teste os links dos anúncios
4. Verifique as permissões de pasta para uploads 