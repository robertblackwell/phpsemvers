# wactl - Whiteacorn control program

This module needs to be kept private as it contains password. Don't push to github.

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