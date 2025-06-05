.PHONY: down up build prod deploy-ansible logs stop restart clean

up:
	docker compose up -d --wait

down:
	docker compose down

build:
	docker compose build --no-cache
	docker compose up -d
	docker compose exec app composer install

prod:
	sudo docker compose down
	sudo docker compose down --volumes --remove-orphans
	sudo docker network prune -f
	sudo docker compose -f compose.prod.yaml build
	sudo docker compose -f compose.prod.yaml up -d --wait
	sudo docker compose -f compose.prod.yaml exec app composer install --optimize-autoloader
	sudo docker compose -f compose.prod.yaml exec app bin/console doctrine:migrations:migrate --no-interaction
	sudo docker compose -f compose.prod.yaml exec app bin/console cache:clear --env=prod

prod-first-time:
	sudo cp .env .env.local
	sudo bin/console cache:clear

logs:
	sudo docker compose -f compose.prod.yaml logs -f

stop:
	sudo docker compose -f compose.prod.yaml down

restart:
	sudo docker compose -f compose.prod.yaml restart

clean:
	sudo docker compose down --volumes --remove-orphans
	sudo docker network prune -f
	sudo docker system prune -f
