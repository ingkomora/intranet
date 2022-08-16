## MOVE TO NEW REMOTE
date: 2022-04-28

## IMPORTANT
test verzija
pokusaj da se popravi upgrade backpack to 5

### composer.json

- PROBLEM:
  
  na live bazi prijavljuje gresku  
  "Doctrine\DBAL\Exception\TableNotFoundException An exception occurred while executing a query: An exception occurred while executing a query: SQLSTATE[42P01]: Undefined table: 7 ERROR: relation "pg_catalog.pg_collation" does not exist"
  
- RESENJE:
  
    instalirati "doctrine/dbal": "^2.1"

