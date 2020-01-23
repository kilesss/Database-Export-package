# Database-Export-package
simple package for export database tables in sql files

use command 
  php artisan databaseExport table1 table2 table3 etc. 
to specify what tables want for export 
or use command 
  php artisan databaseExport all 
 to export all tables in database
 
 all sql files will be stora in storage/sqls . Don`t need to create folder sqls , the scrtipt create it automaticaly
