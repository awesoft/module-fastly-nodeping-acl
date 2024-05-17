# Magento 2 - Fastly-Nodeping ACL

## Description
Automatically update a Fastly ACL with Nodeping's IP addresses.

## Prerequisites
- Fastly module must be installed.

## Features
- Easily enable or disable this feature.
- Option to update IPv4, IPv6 or both addresses.
- Option which Fastly ACL will be updated.
- Option to select how frequent the update runs.

## Installation
- Via composer (Recommended)
  ```shell
  composer require awesoft/module-fastly-nodeping-acl
  ```
- Manual installation
    - Clone or download the latest release: https://github.com/awesoft/module-fastly-nodeping-acl/releases/latest/
    - Extract all files to `app/code/Awesoft/FastlyNodepingAcl/`
    - Run `setup:upgrade` command
      ```shell
      php bin/magento setup:upgrade
      ```
