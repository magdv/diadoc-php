help:
	@echo "\n\
Usage: \n\n\
    make { generate-proto               Гененрирование proto файлов\n\
         | fix-style                    Исправить котостайл\n\
         | rector                       Запуск ректора\n\
         }\n\
    "

generate-proto:
	php generate.php

fix-style:
	vendor/bin/php-cs-fixer fix src

rector:
	vendor/bin/rector process src