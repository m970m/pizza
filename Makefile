up:
	docker compose -f Docker/docker-compose.yaml up -d

down:
	docker compose -f Docker/docker-compose.yaml down

db:
	docker exec -it pizza-db mysql -u root -p

bash:
	docker exec -it pizza-app bash