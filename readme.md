#phpsemvers - Version bumper for php projects

This project provides an executable that can be used in php projects 
to bump (major|minor|patch) version numbers of the for __vn.n.n.

The client php project must be using git for version control as __phpsemvers__
keeps track of the latest version through git tags. 

And expects:

- a git remote called `origin`
- a __php_semvers__ config file called `.phpsemvers.json` in the client project top directory.

Whenever a version is __bumped__ optionally a php file in the client project
can be updated with a string representation of the latest version.

This tool was built for my own personal use.

## Install

Use __composer__.

I install it globally with compoer on my development machines.

## Using

### Installed locally in a php project

Run the following to get a list of commands.

```bash
./vendor/bin/phpsemvers list
```

### Installed globally

Run the following to get a list of commands.

```bash
~/.composer/bin/phpsemvers list
```
Would make sense to add `~/.composer/bin` to your PATH variable.

### Thereafter

Use the help features of __phpsemvers__ to find out how to use it.

