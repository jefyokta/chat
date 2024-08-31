# ChatApp

Built with php native + Openswoole


## requirement

- php 8.2+
- openswoole (php extension)

```
pecl install openswoole
```

## start

- Clone Project

```
git clone https://github.com/jefyokta/chat.git
```

- Install php package

```
composer install
```

- Creating app keys

```
php okta generate-keys
```


- Run Migration

```
 php okta migrate
```
- Run Seeder (optional)

```
 php okta dbseed
```

- Start Http server And Websocket Server

```
php okta start
```

```
php okta ws
```

## done !

hope you like it :)

-jefyokta