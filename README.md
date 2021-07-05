## IMPORTANT

### composer.json

- PROBLEM:
  
  na live bazi prijavljuje gresku  
  "Doctrine\DBAL\Exception\TableNotFoundException An exception occurred while executing a query: An exception occurred while executing a query: SQLSTATE[42P01]: Undefined table: 7 ERROR: relation "pg_catalog.pg_collation" does not exist"
  
- RESENJE:
  
    instalirati "doctrine/dbal": "^2.1"

