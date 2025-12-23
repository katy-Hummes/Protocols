# Protocols
# Protocols
O projeto apresenta um sistema de uma prefeitura, tem 3 tipos de usuários, o Administrador da TI, Administrador do sistema e o Atendente, onde o atendente pode criar pessoas e os protocolos da Prefeitura o Administrador do sistema pode criar os atendentes e também tem acesso adicional as Departamentos e também as Auditorias, já o Administrador da TI tem livre acesso a tudo.

## Preview


### Technical information:

- Backend:
Neste projeto foram utilizados php, Laravel e Inertia para desenvolvimento Backend
e para o banco de dados usei MySQL.

- Front-end:
Desenvolvi uma interface simples usando Vue.js, Tailwind CSS
e também vuetify para os componentes.

### How to use:
  
- A versão do PHP utilizada no Projeto é PHP 8.2.11
- clique no botão verde acima (<> código) e copie o link: https://github.com/KatyHummes/Protocols.git
- configure o banco de dados com .env
- Copie o arquivo **.env.example** para **.env** e configure as variáveis de ambiente relacionadas ao banco de dados
- execute os comandos:
```
composer install
```
```
npm install
```
- então execute o comando
```
php artisan key:generate
```
- abra o servidor:
```
php artisan serve
```
- deixe o npm em execução:
```
npm run dev
```
- execute as migrações: (seeds are optional)
```
php artisan migrate --seed
```
and finally run this command:
```
php artisan storage:link
```
- access the URL: http://127.0.0.1:8000/

## Links das Bibliotecas e outros usados no Projeto:

https://jetstream.laravel.com/ para a autenticação e configurações iniciais,
https://vuetifyjs.com/en/ e https://tailwindcss.com/ para alguns componentes e estruturas do front-end,
https://undraw.co/illustrations para os svgs e o https://heroicons.com/ para os icones.