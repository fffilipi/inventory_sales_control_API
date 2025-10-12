# Documentação Swagger - API de Controle de Estoque e Vendas

## 📋 Visão Geral

A API possui documentação interativa completa via Swagger/OpenAPI, permitindo que desenvolvedores testem e entendam todos os endpoints disponíveis de forma visual e interativa.

## 🔗 Acesso à Documentação

### URL da Documentação
```
http://localhost:8080/api/documentation
```

### Acesso Direto
```
http://localhost:8080/api/documentation/index.html
```

## 🚀 Como Usar

### 1. **Acessar a Documentação**
- Abra o navegador e acesse `http://localhost:8080/api/documentation`
- A interface Swagger UI será carregada automaticamente

### 2. **Autenticação**
- Clique no botão **"Authorize"** no topo da página
- Cole seu token JWT no formato: `Bearer SEU_TOKEN_AQUI`
- Clique em **"Authorize"** para ativar a autenticação

### 3. **Testar Endpoints**
- Expanda qualquer endpoint clicando nele
- Clique em **"Try it out"**
- Preencha os parâmetros necessários
- Clique em **"Execute"** para testar

## 📚 Estrutura da Documentação

### **Tags Organizadas**
- 🔐 **Autenticação** - Login, registro, logout
- 📦 **Produtos** - CRUD de produtos
- 📊 **Estoque** - Controle de estoque
- 💰 **Vendas** - Processamento de vendas

### **Schemas Definidos**
- **Product** - Estrutura de produtos
- **InventoryItem** - Item de estoque consolidado
- **Sale** - Venda com itens
- **SaleItem** - Item individual de venda
- **User** - Dados do usuário
- **ApiResponse** - Resposta padrão de sucesso
- **ApiError** - Resposta padrão de erro

### **Requests/Responses**
- **LoginRequest** - Dados de login
- **RegisterRequest** - Dados de registro
- **ProductRequest** - Dados de produto
- **InventoryRequest** - Dados de estoque
- **SaleRequest** - Dados de venda
- **TokenResponse** - Resposta com token

## 🔧 Funcionalidades da Documentação

### **1. Interface Interativa**
- ✅ **Teste direto** dos endpoints
- ✅ **Validação automática** de dados
- ✅ **Exemplos** de request/response
- ✅ **Códigos de status** HTTP explicados

### **2. Autenticação Integrada**
- ✅ **Bearer Token** configurado
- ✅ **Teste de endpoints protegidos**
- ✅ **Gerenciamento de sessão**

### **3. Exemplos Práticos**
- ✅ **Dados reais** dos seeders
- ✅ **Cenários de uso** comuns
- ✅ **Casos de erro** documentados

## 📖 Exemplos de Uso

### **1. Fluxo Completo de Teste**

#### Passo 1: Login
```json
POST /api/auth/login
{
    "email": "admin@inventory.com",
    "password": "password123"
}
```

#### Passo 2: Criar Produto
```json
POST /api/products
{
    "sku": "TEST001",
    "name": "Produto Teste",
    "description": "Descrição do produto",
    "cost_price": 100.00,
    "sale_price": 150.00
}
```

#### Passo 3: Adicionar Estoque
```json
POST /api/inventory
{
    "product_id": 1,
    "quantity": 10
}
```

#### Passo 4: Realizar Venda
```json
POST /api/sales
{
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        }
    ]
}
```

### **2. Teste de Validações**

#### Produto com SKU Duplicado
```json
POST /api/products
{
    "sku": "PROD001", // SKU já existe
    "name": "Produto Duplicado",
    "cost_price": 100.00,
    "sale_price": 150.00
}
```
**Resultado**: Erro 422 - SKU já está sendo usado

#### Venda com Estoque Insuficiente
```json
POST /api/sales
{
    "items": [
        {
            "product_id": 1,
            "quantity": 100 // Mais que o disponível
        }
    ]
}
```
**Resultado**: Erro 422 - Estoque insuficiente

## 🛠️ Comandos para Manutenção

### **Regenerar Documentação**
```bash
# Regenerar documentação após mudanças
./vendor/bin/sail artisan l5-swagger:generate
```

### **Limpar Cache**
```bash
# Limpar cache da documentação
./vendor/bin/sail artisan cache:clear
```

### **Verificar Configuração**
```bash
# Verificar se o Swagger está configurado
./vendor/bin/sail artisan config:show l5-swagger
```

## 📁 Arquivos de Configuração

### **Configuração Principal**
- `config/l5-swagger.php` - Configurações do Swagger
- `app/Http/Controllers/Controller.php` - Schemas principais
- `app/Http/Controllers/Swagger/` - Anotações por controller

### **Arquivos Gerados**
- `storage/api-docs/` - Documentação JSON gerada
- `resources/views/vendor/l5-swagger/` - Views do Swagger UI

## 🎯 Benefícios da Documentação Swagger

### **Para Desenvolvedores**
- ✅ **Interface visual** intuitiva
- ✅ **Testes em tempo real** dos endpoints
- ✅ **Exemplos práticos** de uso
- ✅ **Validação automática** de dados

### **Para Integração**
- ✅ **Especificação OpenAPI** padrão
- ✅ **Geração de clientes** automática
- ✅ **Documentação sempre atualizada**
- ✅ **Versionamento** da API

### **Para Manutenção**
- ✅ **Documentação viva** no código
- ✅ **Sincronização automática** com mudanças
- ✅ **Testes integrados** na documentação
- ✅ **Padrões consistentes** de resposta

## 🔄 Atualizações

### **Quando Regenerar**
- ✅ **Novos endpoints** adicionados
- ✅ **Mudanças em schemas** existentes
- ✅ **Alterações em validações**
- ✅ **Novos códigos de erro**

### **Processo de Atualização**
1. **Modificar** anotações nos controllers
2. **Executar** `./vendor/bin/sail artisan l5-swagger:generate`
3. **Testar** na interface Swagger
4. **Validar** exemplos e respostas

## 📞 Suporte

### **Problemas Comuns**

#### Documentação não carrega
```bash
# Verificar se o servidor está rodando
./vendor/bin/sail artisan serve --host=0.0.0.0 --port=8080

# Regenerar documentação
./vendor/bin/sail artisan l5-swagger:generate
```

#### Erro 404 na documentação
```bash
# Verificar rotas
./vendor/bin/sail artisan route:list | grep documentation

# Limpar cache de rotas
./vendor/bin/sail artisan route:clear
```

#### Anotações não aparecem
```bash
# Verificar sintaxe das anotações
./vendor/bin/sail artisan l5-swagger:generate

# Verificar logs de erro
./vendor/bin/sail logs
```

## 🎉 Conclusão

A documentação Swagger da API de Controle de Estoque e Vendas fornece uma interface completa e interativa para desenvolvedores testarem e entenderem todos os endpoints disponíveis. Com exemplos práticos, validações automáticas e interface visual intuitiva, facilita significativamente o processo de integração e desenvolvimento.

Acesse `http://localhost:8080/api/documentation` e comece a explorar a API de forma interativa! 🚀
