# DB Snapshot plugin for Craft CMS 3.x

Store Craft CMS database backups in an S3 (or compatible) bucket. Also supports loading those backups for local development, etc.

## Requirements

This plugin requires Craft CMS 3.x and an S3-compatible storage account.

## Installation

To install the plugin, follow these instructions.

1.  Open your terminal and go to your Craft project:

        cd /path/to/project

2.  Then tell Composer to load the plugin:

        composer require jimbojsb/db-snapshot

3.  In the Control Panel, go to Settings → Plugins and click the “Install” button for DB Snapshot.

## Configuring DB Snapshot

Configuration is supported in the Craft admin panel.

Snapshot filename can contain Twig expressions: `db_snapshot_{{now|date("Ymd-His")}}.sql`

All fields support Craft environment variable references (For example, setting Access Key to `$ACCESS_KEY`)

Be aware that if you use a timestamp in the filename, you will need to provide a filename when using the load action.

## Using DB Snapshot

```bash
./craft db-snapshot/snapshot/create
```

```bash
./craft db-snapshot/snapshot/load
```

```bash
./craft db-snapshot/snapshot/load --filename=db_snapshot_2020-02-29-123456.sql
```

Brought to you by [Josh butts](https://github.com/jimbojsb)
