# Exemplos Práticos - Swagger API

## 🚀 Guia Rápido de Uso

### **1. Acessar a Documentação**
```
http://localhost:8080/api/documentation
```

### **2. Fluxo Completo de Teste**

#### **Passo 1: Login**
1. Expanda **"Autenticação"** → **"POST /api/auth/login"**
2. Clique **"Try it out"**
3. Use os dados:
```json
{
    "email": "admin@inventory.com",
    "password": "password123"
}
```
4. Clique **"Execute"**
5. **Copie o token** da resposta (campo `data.token`)

#### **Passo 2: Autorizar**
1. Clique no botão **"Authorize"** (🔒) no topo
2. Cole o token no formato: `Bearer SEU_TOKEN_AQUI`
3. Clique **"Authorize"**

#### **Passo 3: Criar Produto**
1. Expanda **"Produtos"** → **"POST /api/products"**
2. Clique **"Try it out"**
3. Use os dados:
```json
{
    "sku": "SWAGGER001",
    "name": "Produto Swagger Test",
    "description": "Produto criado via Swagger",
    "cost_price": 50.00,
    "sale_price": 75.00
}
```
4. Clique **"Execute"**
5. **Anote o ID** do produto criado

#### **Passo 4: Adicionar Estoque**
1. Expanda **"Estoque"** → **"POST /api/inventory"**
2. Clique **"Try it out"**
3. Use os dados (substitua `1` pelo ID do produto):
```json
{
    "product_id": 1,
    "quantity": 20
}
```
4. Clique **"Execute"**

#### **Passo 5: Consultar Estoque**
1. Expanda **"Estoque"** → **"GET /api/inventory"**
2. Clique **"Try it out"**
3. Clique **"Execute"**
4. Veja o produto com cálculos automáticos

#### **Passo 6: Realizar Venda**
1. Expanda **"Vendas"** → **"POST /api/sales"**
2. Clique **"Try it out"**
3. Use os dados (substitua `1` pelo ID do produto):
```json
{
    "items": [
        {
            "product_id": 1,
            "quantity": 3
        }
    ]
}
```
4. Clique **"Execute"**
5. Veja a venda processada com valores calculados

#### **Passo 7: Consultar Venda**
1. Expanda **"Vendas"** → **"GET /api/sales/{id}"**
2. Clique **"Try it out"**
3. Use o ID da venda criada no passo anterior
4. Clique **"Execute"**
5. Veja os detalhes completos da venda

## 🧪 Testes de Validação

### **Teste 1: SKU Duplicado**
```json
POST /api/products
{
    "sku": "SWAGGER001", // Mesmo SKU do produto criado
    "name": "Produto Duplicado",
    "cost_price": 100.00,
    "sale_price": 150.00
}
```
**Resultado esperado**: Erro 422 - "Este SKU já está sendo usado"

### **Teste 2: Estoque Insuficiente**
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
**Resultado esperado**: Erro 422 - "Estoque insuficiente"

### **Teste 3: Produto Inexistente**
```json
POST /api/inventory
{
    "product_id": 999, // ID que não existe
    "quantity": 10
}
```
**Resultado esperado**: Erro 422 - "O ID do produto fornecido não existe"

### **Teste 4: Dados Inválidos**
```json
POST /api/products
{
    "sku": "", // SKU vazio
    "name": "", // Nome vazio
    "cost_price": -10, // Preço negativo
    "sale_price": "invalid" // Preço inválido
}
```
**Resultado esperado**: Erro 422 com múltiplas validações

## 📊 Teste de Estoque em Lote

### **Adicionar Múltiplos Produtos**
```json
POST /api/inventory
[
    {
        "product_id": 1,
        "quantity": 5
    },
    {
        "product_id": 2,
        "quantity": 10
    },
    {
        "product_id": 3,
        "quantity": 3
    }
]
```

## 💰 Teste de Venda Complexa

### **Venda com Múltiplos Itens**
```json
POST /api/sales
{
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        },
        {
            "product_id": 2,
            "quantity": 1
        },
        {
            "product_id": 3,
            "quantity": 3
        }
    ]
}
```

## 🔐 Teste de Autenticação

### **Token Inválido**
1. Use um token inválido: `Bearer token_invalido`
2. Tente acessar qualquer endpoint protegido
3. **Resultado esperado**: Erro 401 - "Token de acesso inválido ou expirado"

### **Sem Token**
1. Clique **"Authorize"** → **"Logout"**
2. Tente acessar qualquer endpoint protegido
3. **Resultado esperado**: Erro 401 - "Token de acesso não fornecido"

## 📈 Análise de Respostas

### **Estrutura de Sucesso**
```json
{
    "success": true,
    "message": "Operação realizada com sucesso",
    "data": { /* dados específicos */ },
    "timestamp": "2025-01-12T10:30:00.000000Z"
}
```

### **Estrutura de Erro**
```json
{
    "success": false,
    "message": "Descrição do erro",
    "errors": { /* detalhes dos erros */ },
    "timestamp": "2025-01-12T10:30:00.000000Z"
}
```

## 🎯 Dicas de Uso

### **1. Sempre Autorize Primeiro**
- Faça login e autorize antes de testar endpoints protegidos
- O token expira em 24 horas

### **2. Use IDs Reais**
- Crie produtos primeiro para ter IDs válidos
- Use os IDs retornados nas operações subsequentes

### **3. Teste Cenários de Erro**
- Teste validações com dados inválidos
- Verifique mensagens de erro personalizadas

### **4. Observe Cálculos Automáticos**
- Estoque: valores totais calculados automaticamente
- Vendas: lucro e margem calculados
- Estoque atualizado após vendas

### **5. Use os Dados dos Seeders**
- Produtos pré-cadastrados: IDs 1-5
- Usuários: admin@inventory.com, teste@inventory.com, vendedor@inventory.com

## 🚀 Próximos Passos

1. **Explore todos os endpoints** disponíveis
2. **Teste diferentes cenários** de uso
3. **Valide as regras de negócio** implementadas
4. **Use a documentação** para integração com frontend
5. **Compartilhe** com outros desenvolvedores

A documentação Swagger torna o desenvolvimento e integração muito mais eficiente! 🎉
