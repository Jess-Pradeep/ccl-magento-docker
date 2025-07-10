#!/bin/bash

DUMP_FILE="ccl_dev_2025-06-05.sql"
DB_CONTAINER="ccl-magento-db-1"
MYSQL_CMD="mysql -u magento -pmagento magento"

TABLES=(
  "catalog_product_entity"
  "catalog_product_entity_datetime"
  "catalog_product_entity_decimal"
  "catalog_product_entity_int"
  "catalog_product_entity_text"
  "catalog_product_entity_varchar"
  "cataloginventory_stock_item"
  "cataloginventory_stock_status"
  "catalog_category_product"
  "url_rewrite"
)

for TABLE in "${TABLES[@]}"; do
  echo "➡️  Restoring: $TABLE"

  # Extract DDL + INSERTs
  sed -n "/DROP TABLE IF EXISTS \`$TABLE\`/,/UNLOCK TABLES;/p" $DUMP_FILE > "tmp_restore_$TABLE.sql"

  # Prepend SET foreign_key_checks = 0;
  echo "SET foreign_key_checks = 0;" | cat - tmp_restore_$TABLE.sql > tmp && mv tmp tmp_restore_$TABLE.sql
  echo "SET foreign_key_checks = 1;" >> tmp_restore_$TABLE.sql

  # Copy file into container
  docker cp tmp_restore_$TABLE.sql $DB_CONTAINER:/tmp/tmp_restore_$TABLE.sql

  # Import inside container
  docker exec -i $DB_CONTAINER sh -c "mysql -u magento -pmagento magento < /tmp/tmp_restore_$TABLE.sql"

  echo "✅ $TABLE restored"
done

echo "♻️  Cleaning up..."
rm tmp_restore_*.sql
echo "🎉 All selected product-related tables restored!"
