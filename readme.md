#php_semvers - Version bumper for php projects

This project provides an executable that can be used in php projects 
to bump (major|minor|patch) version numbers of the for __vn.n.n.

The client php project must be using git for version control as __php_semvers__
keeps track of the latest version through git tags. 

And expects:

- a git remote called `origin`
- a __php_semvers__ config file called `php_semvers.json` in the client project top directory.

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
./vendor/bin/php_semvers list
```

### Installed globally

Run the following to get a list of commands.

```bash
~/.composer/bin/php_semvers list
```
Would make sense to add `~/.composer/bin` to your PATH variable.

### Thereafter

Use the help features of __php_semvers__ to find out how to use it.

module needs to be kept private as it contains password. Don't push to github.

NOTE: the `makefile` appears to build a __phar__, but this is not correct.

This package should oonly be installed on my development OSX machine.

That is achieved by

```
subl version.json #and change the version number
git add -A
git commit -m"note why this update"
git push local
git tag -avx.y.z #use the new version number
git push --tag
#edit ~/.composer/composer.json to require wactl with the new version number
# delete the composer lock file
composer global update 

```    

Since added bumpversion to this project

```

	git add -A
	git commit -a -m" .... "
	bumpversion major|minor|patch
	git add -A
	git commit -a -m""

```