# Documentação Completa do Git

## Índice

- [1. Introdução ao Git](#1-introdução-ao-git)
- [2. Comandos Básicos](#2-comandos-básicos)
  - [2.1 git diff](#21-git-diff)
  - [2.2 git status](#22-git-status)
  - [2.3 git add](#23-git-add)
- [3. Revertendo Mudanças](#3-revertendo-mudanças)
  - [3.1 git checkout](#31-git-checkout)
  - [3.2 git reset](#32-git-reset)
  - [3.3 git revert](#33-git-revert)
- [4. Trabalhando com Tags](#4-trabalhando-com-tags)
- [5. Trabalhando com Branches](#5-trabalhando-com-branches)
  - [5.1 Criando Branches](#51-criando-branches)
  - [5.2 Navegando entre Branches](#52-navegando-entre-branches)
  - [5.3 Fazendo Merge](#53-fazendo-merge)
- [6. Arquivo .gitignore](#6-arquivo-gitignore)
  - [6.1 Ignorando Arquivos por Extensão](#61-ignorando-arquivos-por-extensão)
  - [6.2 Ignorando Arquivos em Pastas Específicas](#62-ignorando-arquivos-em-pastas-específicas)
- [7. Dicas e Boas Práticas](#7-dicas-e-boas-práticas)

---

## 1. Introdução ao Git

O Git é como um "historiador" do seu código. Ele guarda todas as versões dos seus arquivos, permitindo que você volte no tempo quando necessário. Imagine que você está escrevendo um livro e, a cada capítulo finalizado, você faz uma "foto" (commit) do estado atual - assim você sempre pode voltar a qualquer capítulo anterior se precisar.

## 2. Comandos Básicos

### 2.1 git diff

O comando `git diff` é como um "detetive" que mostra exatamente o que mudou nos seus arquivos.

```bash
git diff arquivo.php
```

**⚠️ Importante:** Para que o `git diff` funcione corretamente, você precisa entender os "estados" dos arquivos:

1. **Working Directory** (Diretório de Trabalho): Onde você faz as modificações
2. **Staging Area** (Área de Preparação): Onde ficam os arquivos preparados para commit
3. **Repository** (Repositório): Onde ficam os commits salvos

**Como usar:**
- `git diff` - Mostra diferenças entre Working Directory e Staging Area
- `git diff --staged` - Mostra diferenças entre Staging Area e último commit
- `git diff arquivo.php` - Mostra diferenças de um arquivo específico

### 2.2 git status

É como um "painel de controle" que mostra o estado atual dos seus arquivos.

```bash
git status
```

**Quando usar:** Sempre que quiser saber o que está acontecendo no seu repositório.

### 2.3 git add

É como "preparar" os arquivos para serem "fotografados" (commitados).

```bash
git add arquivo.php          # Adiciona um arquivo específico
git add .                    # Adiciona todos os arquivos modificados
```

## 3. Revertendo Mudanças

Imagine que você está pintando um quadro. Às vezes você faz uma pincelada errada e precisa "voltar atrás". No Git, temos várias formas de fazer isso:

### 3.1 git checkout

É como uma "máquina do tempo" para arquivos individuais. Use quando quiser descartar mudanças que ainda não foram commitadas.

```bash
git checkout -- arquivo.php
```

**Analogia:** É como usar uma "borracha" para apagar as mudanças recentes e voltar ao estado do último commit.

**Quando usar:**
- Quando você modificou um arquivo e quer voltar ao estado do último commit
- Quando o código começou a dar erros após suas modificações
- ANTES de fazer commit das mudanças

**⚠️ Cuidado:** Este comando é irreversível! As mudanças não salvas serão perdidas para sempre.

**Exemplo prático:**
```bash
# Você modificou o arquivo e ele está com bugs
git status                                    # Vê que o arquivo foi modificado
git checkout -- src/Controller/HelloController.php  # Volta ao estado original
```

### 3.2 git reset

É como "voltar no tempo" para commits anteriores. Use quando já commitou algo por engano.

```bash
git reset HEAD~1      # Volta 1 commit (mantém as mudanças no Working Directory)
git reset --soft HEAD~1   # Volta 1 commit (mantém mudanças na Staging Area)
git reset --hard HEAD~1   # Volta 1 commit (apaga TUDO - muito perigoso!)
```

**Analogia:** É como "desfazer" no Word, mas para commits inteiros.

**Quando usar:**
- Quando fez um commit com erro
- Quando quer modificar a mensagem do último commit
- Quando commitou arquivos por engano

**Tipos de reset:**
- `--soft`: Mais "gentil" - mantém suas mudanças na Staging Area
- `--mixed` (padrão): Médio - mantém mudanças no Working Directory
- `--hard`: Mais "violento" - apaga tudo (use com cuidado!)

### 3.3 git revert

É como criar um "anti-commit" que desfaz mudanças sem apagar o histórico.

```bash
git revert HEAD          # Reverte o último commit
git revert abc123        # Reverte um commit específico
```

**Quando usar:** Quando quer desfazer um commit que já foi compartilhado com outras pessoas.

## 4. Trabalhando com Tags

Tags são como "marcos" ou "etiquetas" que marcam pontos importantes do seu projeto.

```bash
git tag -a v1.0 -m "Versão 1.0 - Primeira versão estável"
git tag -a v2.1 -m "Versão 2.1 - Correção de bugs críticos"
```

**Analogia:** É como colocar uma placa dizendo "Aqui foi construída a versão 1.0" em um ponto específico da história do seu código.

**Quando usar:**
- Para marcar versões de release (v1.0, v2.0, etc.)
- Para comparar diferentes versões
- Para facilitar o deploy de versões específicas

**Comandos úteis:**
```bash
git tag                   # Lista todas as tags
git show v1.0            # Mostra detalhes da tag v1.0
git tag -d v1.0          # Deleta a tag v1.0
```

## 5. Trabalhando com Branches

Branches são como "universos paralelos" do seu código. Imagine que você tem um projeto principal (main) e quer experimentar uma nova funcionalidade sem bagunçar o código principal.

### 5.1 Criando Branches

```bash
git checkout -b nova-funcionalidade    # Cria e muda para a nova branch
git branch nova-funcionalidade        # Apenas cria a branch (não muda para ela)
```

**Analogia:** É como criar uma "cópia" do seu projeto para experimentar sem medo de quebrar o original.

**Quando usar:**
- Toda vez que for desenvolver uma nova funcionalidade
- Para correção de bugs específicos
- Para experimentar ideias sem afetar o código principal

### 5.2 Navegando entre Branches

```bash
git branch              # Lista todas as branches
git checkout main       # Muda para a branch main
git checkout feature-login  # Muda para a branch feature-login
```

### 5.3 Fazendo Merge

Merge é como "juntar" as mudanças de uma branch com outra.

```bash
git checkout main           # Vai para a branch de destino
git merge nova-funcionalidade  # Traz as mudanças da outra branch
```

**Processo completo:**
```bash
# 1. Criar e trabalhar na nova branch
git checkout -b feature-carrinho
# ... fazer modificações e commits ...

# 2. Voltar para main e fazer merge
git checkout main
git merge feature-carrinho

# 3. (Opcional) Deletar a branch após merge
git branch -d feature-carrinho
```

**Tipos de merge:**
- **Fast-forward**: Quando não houve commits na main enquanto você trabalhava
- **3-way merge**: Quando houve commits paralelos (Git cria um commit de merge)

## 6. Arquivo .gitignore

O `.gitignore` é como uma "lista de ignorados" - arquivos que o Git deve fingir que não existem.

### 6.1 Ignorando Arquivos por Extensão

**❌ Forma incorreta (da anotação original):**
```
.jpg/
```

**✅ Forma correta:**
```
*.jpg
*.png
*.log
*.tmp
```

O `*` significa "qualquer nome de arquivo". Então `*.jpg` ignora todos os arquivos terminados em `.jpg`.

### 6.2 Ignorando Arquivos em Pastas Específicas

**❌ Forma incorreta (da anotação original):**
```
/teste/*.jpg
```

**✅ Formas corretas:**
```bash
teste/*.jpg              # Ignora .jpg apenas na pasta teste
**/teste/*.jpg          # Ignora .jpg em qualquer pasta chamada teste
logs/                   # Ignora toda a pasta logs
node_modules/          # Ignora toda a pasta node_modules
```

**Exemplo de .gitignore completo:**
```bash
# Arquivos de sistema
.DS_Store
Thumbs.db

# IDEs
.vscode/
.idea/
*.swp

# Dependências
node_modules/
vendor/

# Arquivos de build
dist/
build/

# Arquivos de log
*.log
logs/

# Arquivos temporários
*.tmp
*.temp

# Arquivos de configuração local
.env
config.local.php

# Imagens grandes (opcional)
*.jpg
*.png
*.gif
```

**Dica importante:** O `.gitignore` só funciona para arquivos que ainda não estão sendo "rastreados" pelo Git. Se você já fez commit de um arquivo, precisa "desrastreá-lo" primeiro:

```bash
git rm --cached arquivo.jpg    # Remove do Git mas mantém no seu computador
```

## 7. Dicas e Boas Práticas

### Fluxo de Trabalho Recomendado

1. **Sempre verifique o status antes de fazer qualquer coisa:**
   ```bash
   git status
   ```

2. **Crie uma branch para cada nova funcionalidade:**
   ```bash
   git checkout -b feature-login
   ```

3. **Faça commits pequenos e frequentes com mensagens claras:**
   ```bash
   git add .
   git commit -m "Adiciona validação de email no formulário de login"
   ```

4. **Teste antes de fazer merge:**
   ```bash
   # Teste sua funcionalidade na branch
   # Só depois faça o merge
   git checkout main
   git merge feature-login
   ```

### Mensagens de Commit

**❌ Ruins:**
- "fix"
- "mudanças"
- "atualização"

**✅ Boas:**
- "Corrige validação de CPF no formulário de cadastro"
- "Adiciona endpoint para listagem de usuários"
- "Remove dependência não utilizada do jQuery"

### Comandos para Situações Comuns

**"Fiz besteira e quero descartar tudo:"**
```bash
git checkout .              # Descarta mudanças em todos os arquivos
git clean -fd              # Remove arquivos novos não rastreados
```

**"Quero ver o que mudou antes de comitar:"**
```bash
git diff                   # Mostra mudanças não adicionadas
git diff --staged          # Mostra mudanças já adicionadas (staged)
```

**"Esqueci de algo no último commit:"**
```bash
git add arquivo-esquecido.php
git commit --amend --no-edit    # Adiciona ao commit anterior sem mudar mensagem
```

**"Quero ver o histórico de commits:"**
```bash
git log --oneline          # Histórico resumido
git log --graph            # Histórico com gráfico de branches
```

---

**Lembre-se:** Git é uma ferramenta poderosa, mas com grande poder vem grande responsabilidade. Sempre tenha backup e teste em branches separadas antes de fazer mudanças na branch principal!
