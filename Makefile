.PHONY: dummy

fakeseed: dummy
	rm toils.sqlite
	touch toils.sqlite
	php artisan migrate
	php artisan db:seed --class=FakeSeeder
