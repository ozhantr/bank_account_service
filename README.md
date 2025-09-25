## Make File
make up:          ## Start containers in detached mode
make down:        ## Stop and remove containers, networks (keeps volumes)
make logs:        ## Tail logs from all containers
make cache-clear: ## Symfony clear-cache
make phpstan:     ## PHPStan
make cs-fix:      ## PHP CS Fixer

## API Endpoints:

### Register
[POST] http://localhost:8080/api/auth/register
{"email":"demo3@de.com","password":"testdemo"}

### Get Token
[POST] http://localhost:8080/api/login_check
{"email":"demo3@de.com","password":"testdemo"}

### Get User Info
[GET] http://localhost:8080/api/accounts/me
- Bearer Token

### Transactions
[POST] http://localhost:8080/api/accounts/{ACCOUNT_ID}/transactions

###### For Deposit Value
{"type":"deposit","amount":"50"}

###### For Withdraw Value
{"type":"withdraw","amount":"20"}

### Transaction List
[GET] http://localhost:8080/api/accounts/{ACCOUNT_ID}/transactions
- Bearer Token


## Notes:
bin/console doctrine:migrations:migrate
bin/console debug:router
bin/console cache:clear
bin/console lexik:jwt:generate-keypair
bin/console security:hash-password
