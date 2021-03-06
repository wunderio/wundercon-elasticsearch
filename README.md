# Wundercon - Elasticsearch workshop

### Requirements
- Install [Vagrant](https://www.vagrantup.com/downloads.html) 1.9.2 or greater
- Install [vagrant-cachier](https://github.com/fgrehm/vagrant-cachier)
 `vagrant plugin install vagrant-cachier`
- Install [Virtualbox](https://www.virtualbox.org/wiki/Downloads) 5.1 or greater. Note version 5.1.24 has a known issue that breaks nfs, do not use it, version 5.1.22 s known to work.
- Make sure you have python2.7 also installed (For OS X should be default).

### Setup

Let Vagrant create your new machine:

`vagrant up`

SSH into your box and build and install Drupal:

```
vagrant ssh
cd /vagrant/drupal
./build.sh create
```

Create the Elasticsearch indices:

```
cd web
drush elasticsearch-helper-setup content_index_node
```

Import the dataset (limit is added to save a bit of time):

```
drush migrate:import movies --limit=50
```

To reindex the content:
```
drush elasticsearch:helper:reindex content_index_node
drush queue-run elasticsearch_helper_indexing
```

Optional - log into Drupal:

```
drush user:login --uri=http://local.wundercon-elasticsearch.com
```

Open [https://local.wundercon-elasticsearch.com](https://local.wundercon-elasticsearch.com) and follow along!
