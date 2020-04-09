ARGUMENTS=$(filter-out $@,$(MAKECMDGOALS))

start_xdebug:
	DOCKER_XDEBUG="remote_host=host.docker.internal remote_enable=1 remote_autostart=1 max_nesting_level=400" docker-compose up -d --force-recreate


start:
	docker-compose up -d --force-recreate


container_bash:
	docker exec -it $(ARGUMENTS) /bin/bash


# e.g.: create_chat_users
db_migration_for:
	docker-compose exec php php artisan make:migration $(ARGUMENTS)


db_migrate_fresh:
	docker-compose exec php php artisan migrate:fresh


clear_cache:
	docker-compose exec php php artisan cache:clear
