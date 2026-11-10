# Terminus Carbon Plugin

[![Terminus v2.x - v3.x Compatible](https://img.shields.io/badge/terminus-2.x%20--%203.x-green.svg)](https://github.com/pantheon-systems/terminus-carbon-plugin/tree/main)

A Terminus plugin for fetching carbon impact and other sustainability data.

## Configuration

These commands require no configuration

## Usage
* `terminus carbon:regions`: Print region info about the Pantheon regions in Google Cloud.
* `terminus carbon:info`: Displays carbon information about a site.
* `terminus carbon:env`: Displays carbon information in addition to a summary of container services for an environment.
* `terminus carbon:org`: Displays the list of sites associated with an organization with carbon data.


## Installation

To install this plugin using Terminus 3:
```
terminus self:plugin:install terminus-carbon-plugin
```

On older versions of Terminus:
```
mkdir -p ~/.terminus/plugins
curl https://github.com/pantheon-systems/terminus-carbon-plugin/archive/main.tar.gz -L | tar -C ~/.terminus/plugins -xvz
```

## Rollback

- **When:** a released version of this plugin is broken for users or CI (for example `terminus carbon:info` fails after an update).
- **Recover:** releases are fixed forward. Revert the bad change on `main` through a PR and tag the next patch release; never delete or move a published tag. Users who can't wait pin the previous version with Composer.
- **Verify:** Packagist (`https://repo.packagist.org/p2/pantheon-systems/terminus-carbon-plugin.json`) lists the new patch first, and `terminus self:plugin:update` picks it up.
- **Who to page:** `#ask-hosting` (owner: developer-experience). Nobody is paged automatically.

Full procedure: [DEPLOYMENT.md](DEPLOYMENT.md#rollback).
