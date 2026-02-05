# image-mime-type-guesser
- Current version : 0.3
- Where to download : https://github.com/rosell-dk/image-mime-type-guesser/tags

# webp-convert
- Current version : 2.6.0
- Where to download : https://github.com/rosell-dk/webp-convert/tags
- To upgrade:
    - Delete the directory vendor/rosell-dk/webp-convert
    - Copy files from the new version, except docs
    - Check differences with git repo, add new files, revert deleted index.php files
    - Update this file

Be aware that 2 files are modified for us to handle incompatible mime types 
and serve the original file even if it's not a JPG, PNG or WEBP image:
- vendor\rosell-dk\webp-convert\src\Serve\ServeConvertedWebP.php
- vendor\rosell-dk\webp-convert\src\Serve\ServeFile.php

# greenlion/php-sql-parser (Prefixed by JPresta\Greenlion)
In the directory of the module:
- Run 'composer update' to get latest version
- Run 'vendor/bin/php-scoper add-prefix --prefix="JPresta" --output-dir="vendor/jpresta/greenlion/php-sql-parser"
- Make sure the file modules/jprestaspeedpack/vendor/jpresta/greenlion/php-sql-parser/src/PHPSQLParser/builders/JoinBuilder.php include the "use use JPresta\Greenlion\PHPSQLParser\exceptions\UnsupportedFeatureException;" and handle the NATURAL JOIN
- In modules/jprestaspeedpack/vendor/jpresta/greenlion/php-sql-parser/src/PHPSQLParser/utils/PHPSQLParserConstants.php add the CURRENT_DATE in $fonctions
- Run 'vendor/bin/autoindex prestashop:add:index vendor/jpresta/greenlion/' to generate missing index.php files needed by Prestashop

# jdorn/sql-formatter
Be aware that this library is included in Prestashop...

# At the end
- Run 'composer install --no-dev' to remove DEV dependencies
- Run 'composer dump-autoload' to update the autoloader
