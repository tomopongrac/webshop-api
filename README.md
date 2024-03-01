# Package installation
1. Clone the repository
2. Since this package is not published on packagist, you need to add  the following to your composer.json file in application on which you want to use this package (they must be on the same level):
```json
"repositories": [
    {
        "type": "path",
        "url": "../webshop-api"
    }
]
```
and under require add:
```json
"require": {
    "tomopongrac/webshop-api": "@dev",
}
```
3. Run `composer update`
4. In main application map packages entities by adding the following to your doctrine.yaml file:
```yaml
doctrine:
    orm:
        mappings:
            WebshopApiBundle:
                is_bundle: false
                type: xml
                dir: '%kernel.project_dir%/vendor/tomopongrac/webshop-api/config/doctrine'
                prefix: 'TomoPongrac\WebshopApiBundle\Entity'
                alias: WebshopApiBundle
        resolve_target_entities:
            TomoPongrac\WebshopApiBundle\Entity\UserWebShopApiInterface: 'App\Entity\User'
```
5. In services.yaml file add the following:
```yaml
imports:
    - { resource: '../vendor/tomopongrac/webshop-api/src/Resources/config/services.yaml' }
services:
    TomoPongrac\WebshopApiBundle\Repository\:
        resource: '../vendor/tomopongrac/webshop-api/src/Repository'
        lazy: true
```

# How to run static analysis
1. Run `composer phpstan`

# How to run code style fixer
1. Run `composer cs-fixer-fix`
2. Run `composer cs-fixer-check` to check if there are any code style issues

# How to add composer command to git hooks
1. Navigate to the .git/hooks directory
2. Create a new file named `pre-commit`
3. Add the following content to the file:
```bash
#!/bin/sh
composer cs-check
composer phpstan
```
4. Make the file executable by running `chmod +x pre-commit`
If you want to skip the pre-commit hook, you can run `git commit --no-verify`
Or if you want to run composer tasks after the commit is done, follow the same steps but name the fiel as `post-commit` instead of `pre-commit`