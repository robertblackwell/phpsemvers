#makefile

PHPCS=@~/.composer/vendor/bin/phpcs -s
# STANDARD=./phpcs.xml
STANDARD=Blackwell

.PHONY:
cs:
	$(PHPCS) --standard=${STANDARD} \
		src/main.php \
		src/CtlTools/SingleCommandApplication.php \
		src/CtlTools/Version.php \
		src/Ctl/Commands//AssetConfigObject.php \
		src/Ctl/Commands//ConfigObject.php \
		src/Ctl/Commands//Remote.php \
		src/Ctl/Commands//Synchronizer.php \
		src/Ctl/Commands//UpdateBaseCommand.php \
		src/Ctl/Commands//UpdateHtaccessCommand.php \
		src/Ctl/Commands//UpdatePHPCommand.php \
		src/Ctl/Commands//VersionCommand.php \
		src/Ctl/Commands//UpdateBaseCommand.php \
		src/Ctl/Commands//UpdateBaseCommand.php \
		src/Ctl/Commands//UpdateBaseCommand.php \
		src/Ctl/Commands//Garmin/BaseCommand.php \
		src/Ctl/Commands//Garmin/DailyCommand.php \
		src/Ctl/Commands//Ird/Deport/AlbumCommand.php \
		src/Ctl/Commands//Ird/Deport/BannerCommand.php \
		src/Ctl/Commands//Ird/Deport/BaseCommand.php \
		src/Ctl/Commands//Ird/Deport/EditorialCommand.php \
		src/Ctl/Commands//Ird/Deport/ItemCommand.php \
		src/Ctl/Commands//Ird/Import/AlbumCommand.php \
		src/Ctl/Commands//Ird/Import/BannerCommand.php \
		src/Ctl/Commands//Ird/Import/BaseCommand.php \
		src/Ctl/Commands//Ird/Import/EditorialCommand.php \
		src/Ctl/Commands//Ird/Import/ItemCommand.php \
		src/Ctl/Commands//Ird/Refresh/AlbumCommand.php \
		src/Ctl/Commands//Ird/Refresh/BannerCommand.php \
		src/Ctl/Commands//Ird/Refresh/BaseCommand.php \
		src/Ctl/Commands//Ird/Refresh/EditorialCommand.php \
		src/Ctl/Commands//Ird/Refresh/ItemCommand.php \
		src/Ctl/Commands//Push/AlbumCommand.php \
		src/Ctl/Commands//Push/BannerCommand.php \
		src/Ctl/Commands//Push/BaseCommand.php \
		src/Ctl/Commands//Push/EditorialCommand.php \
		src/Ctl/Commands//Push/ItemCommand.php \
		src/Ctl/Commands//Push/NinaRobCommand.php \
		src/Ctl/Commands//Push/SomethingCommand.php 
