ARGUMENTS=$(filter-out $@,$(MAKECMDGOALS))

start-xdebug:
	DOCKER_XDEBUG="remote_host=host.docker.internal remote_enable=1 remote_autostart=0 max_nesting_level=400" docker-compose up --force-recreate php

start:
	docker-compose up --force-recreate php


container_bash:
	docker exec -it $(ARGUMENTS) /bin/bash
