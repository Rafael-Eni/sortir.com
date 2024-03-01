**Documentation de déploiement pour l'application Symfony**
===========================================================

**Introduction**
----------------

Cette documentation détaille les étapes nécessaires pour déployer l'application Symfony sur un serveur en production. Assurez-vous de suivre attentivement chaque étape pour garantir un déploiement réussi.

**Prérequis**
-------------

Avant de commencer le déploiement, assurez-vous que votre environnement de déploiement respecte les prérequis suivants :

-   PHP (version compatible avec Symfony)
-   Base de données (MySQL, PostgreSQL, etc.)
-   Composer installé sur le serveur

**Étapes de déploiement**
-------------------------

### **1\. Cloner le dépôt Git**

Commencez par cloner le dépôt Git de votre application sur le serveur de production :

```
git clone https://github.com/Rafael-Eni/sortir.com.git
```

### **2\. Installer les dépendances**

Accédez au répertoire du projet et utilisez Composer pour installer les dépendances :

```
composer install --no-dev
```

L'option `--no-dev` est utilisée pour exclure les dépendances de développement, ce qui réduit la taille de l'installation.

### **3\. Configuration de l'environnement**

```
bashCopy codecp .env.dist .env
```

Éditez le fichier `.env` et configurez les paramètres tels que la connexion à la base de données, les clés secrètes, etc.

### **4\. Préparation de la base de données**

Si votre application utilise une base de données, exécutez les migrations pour créer le schéma de base de données :

```
symfony console doctrine:migrations:migrate 
```


### **Conclusion**
Une fois ces étapes terminées, votre application Symfony devrait être déployée avec succès sur votre serveur de production.