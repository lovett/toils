.PHONY: dummy

fakeseed: dummy
	php artisan migrate:fresh
	php artisan db:seed --class=FakeSeeder

lint: dummy
	phpmd app text phpmd.xml
