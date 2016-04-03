# Multisite Directory

[![Build Status for develop branch](https://travis-ci.org/meitar/multisite-directory.svg?branch=develop)](https://travis-ci.org/meitar/multisite-directory)

A WordPress plugin that makes it easy to add a Network-wide "Site Directory" to your WP Multisite install.

# Running tests

Multisite Directory is intended only for WP Multisite installs (as the name implies). Therefore, when you run tests locally, do:

```sh
WP_MULTISITE=1 phpunit
```

If you don't set the `WP_MULTISITE` environment variable, the tests will still run, but you will be testing a single-site install, and the plugin doesn't really do anything interesting on single-site installs (for obvious reasons).
