#!/bin/bash
set -e # termina o script com um código diferente de 0 se alguma coisa falhar

# pull requests e commits para outras branches diferentes da master
# não devem fazer o deploy, isso é opcional caso queira deletar as próximas 6 linhas
# fique a vontade
SOURCE_BRANCH="develop"

# entre na pasta onde está o build do seu projeto e inicie um novo repositório git
cd packages/WalterNascimentoBarroso/CrudGenerator
git init

# inside this git repo we'll pretend to be a new user
# dentro desse repositório nós pretendemos ser um novo usuário
git config user.name "Travis CI"
git config user.email "walter.nascimento.barroso@gmail.com"

# O primeiro e único commit do seu repositório terá
# todos os arquivos presentes e a mensagem do commit será "Deploy to GitHub Pages"
git add .
git commit -m "Deploy to GitHub"

# Forçando o push do master para a branch gh-pages (Toda história anterior da branch
# gh-pages será perdido, pois vamos substituí-lo.)  Redirecionamos qualquer saída para
# /dev/null para ocultar quaisquer dados de credenciais sensíveis que de outra forma possam ser expostos.
# tokens GH_TOKEN e GH_REF serão fornecidos como variáveis de ambiente Travis CI
# GH_TOKEN => chave
# GH_REF => repositorio
git push --force --quiet "https://${GH_TOKEN}@walternascimentobarroso/laravel-crud-generator.git" master:master > /dev/null 2>&1

# exit the script
exit 0
