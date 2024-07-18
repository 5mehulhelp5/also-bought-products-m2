## Documentation

- Installation guide: https://www.mavenbird.com/install-magento-2-extension/
- User Guide: https://docs.mavenbird.com/who-bought-this-also-bought/index.html
- Product page: https://www.mavenbird.com/magento-2-who-bought-this-also-bought/
- Get Support: https://www.mavenbird.com/contact.html or support@mavenbird.com
- Changelog: https://www.mavenbird.com/releases/who-bought-also-bought
- License agreement: https://www.mavenbird.com/LICENSE.txt

## How to install

### Install ready-to-paste package 

- Download the latest version at https://store.mavenbird.com/my-downloadable-products.html
- Installation guide: https://www.mavenbird.com/install-magento-2-extension/

## How to upgrade

1. Backup
Backup your Magento code, database before upgrading.
2. Remove AlsoBought folder 
In case of customization, you should backup the customized files and modify in newer version. 
Now you remove `app/code/Mavenbird/AlsoBought` folder. In this step, you can copy override AlsoBought folder but this may cause of compilation issue. That why you should remove it.

3. Upload new version
Upload this package to Magento root directory
4. Run command line:

```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```


## FAQs

#### Q: My site is down
A: Please follow this guide: https://www.mavenbird.com/blog/magento-site-down.html
